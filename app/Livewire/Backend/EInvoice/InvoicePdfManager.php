<?php

namespace App\Livewire\Backend\EInvoice;

use Livewire\Component;
use App\Models\ModInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use horstoeko\zugferd\ZugferdProfiles;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfBuilder;
use Illuminate\Support\Facades\Auth;

class InvoicePdfManager extends Component
{
    public $invoiceId;

    public function generatePdf($id, $zugferd = false, $templatePath = 'public/assets/e-invoice/template-1/')
    {
        $customerId = Auth::guard('customer')->id();

        $invoice = ModInvoice::with('recipient', 'items', 'creator')
            ->where('id', $id)
            ->where('customer_id', $customerId) // 👈 statt user_id
            ->firstOrFail();

        $creator   = $invoice->creator;
        $recipient = $invoice->recipient;
        $items     = $invoice->items;

        // Gruppierte Steuerdetails berechnen
        $taxGroups = $items->groupBy('tax_rate');
        $taxDetails = $taxGroups->map(function ($items, $rate) {
            $netAmount   = $items->sum(fn($item) => $item->quantity * $item->unit_price);
            $taxAmount   = round($netAmount * ($rate / 100), 2);
            $grossAmount = $netAmount + $taxAmount;

            return [
                'rate'  => number_format($rate, 2, '.', ''),
                'net'   => round($netAmount, 2),
                'tax'   => round($taxAmount, 2),
                'gross' => round($grossAmount, 2),
            ];
        })->values();

        $totalNet   = round($taxDetails->sum('net'), 2);
        $totalTax   = round($taxDetails->sum('tax'), 2);
        $totalGross = round($taxDetails->sum('gross'), 2);

        $directory = storage_path('app/public/invoices');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $stylesheet = "{$templatePath}style.css";

        $basePdfPath = "{$directory}/{$invoice->invoice_number}_base.pdf";
        $pdf = Pdf::loadView('pdf.invoices.invoice', compact(
            'invoice',
            'creator',
            'recipient',
            'items',
            'totalNet',
            'totalTax',
            'totalGross',
            'stylesheet',
            'taxDetails'
        ));
        $pdf->save($basePdfPath);

        if ($zugferd) {
            $pdfPath = $this->generateZugferdPdf($invoice, $basePdfPath, $directory);
            session()->flash('message', 'ZUGFeRD-PDF erfolgreich erstellt!');
        } else {
            session()->flash('message', 'Basis-PDF erfolgreich erstellt!');
            $pdfPath = $basePdfPath;
        }

        $invoice->update(['pdf_path' => $pdfPath]);
    }

    public function generateZugferdPdf($invoice, $basePdfPath, $directory)
    {
        $creator   = $invoice->creator;
        $recipient = $invoice->recipient;

        $document = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_EN16931);

        $document
            ->setDocumentInformation(
                $invoice->invoice_number,
                "380",
                new \DateTime($invoice->invoice_date),
                "EUR"
            )
            ->setDocumentSeller(
                $creator->company_name,
                "549910"
            )
            ->addDocumentSellerTaxRegistration("VA", $creator->tax_number ?? 'Unbekannt')
            ->setDocumentSellerAddress(
                $creator->address,
                "",
                "",
                $creator->postal_code,
                $creator->city,
                $creator->country
            )
            ->setDocumentSellerContact(
                "{$creator->first_name} {$creator->last_name}",
                "Geschäftsführer",
                $creator->phone ?? '',
                "",
                $creator->email ?? ''
            )
            ->setDocumentBuyer($recipient->name ?? 'Unbekannt', "Kundennummer")
            ->setDocumentBuyerAddress(
                $recipient->address,
                "",
                "",
                $recipient->zip_code,
                $recipient->city,
                $recipient->country
            )
            ->addDocumentPaymentTerm($recipient->payment_terms ?? 'Zahlbar innerhalb von 30 Tagen netto.')
            ->setDocumentSupplyChainEvent(new \DateTime($invoice->invoice_date));

        $taxGroups = $invoice->items->groupBy('tax_rate');
        foreach ($taxGroups as $rate => $items) {
            $netAmount = $items->sum(fn($item) => $item->quantity * $item->unit_price);
            $totalTax  = round($netAmount * ($rate / 100), 2);

            $document->addDocumentTax("S", "VAT", (float) $rate, $netAmount, $totalTax);
        }

        foreach ($invoice->items as $item) {
            $document->addNewPosition($item->id)
                ->setDocumentPositionProductDetails(
                    $item->description ?? 'Unbekannt',
                    "",
                    "P{$item->id}",
                    null,
                    "0160",
                    "4000050986428"
                )
                ->setDocumentPositionNetPrice($item->unit_price ?? 0)
                ->setDocumentPositionQuantity($item->quantity ?? 1, "H87")
                ->addDocumentPositionTax('S', 'VAT', $item->tax_rate ?? 0)
                ->setDocumentPositionLineSummation(($item->unit_price ?? 0) * ($item->quantity ?? 1));
        }

        $mergedPdfPath = "{$directory}/{$invoice->invoice_number}_zugferd.pdf";

        $pdfBuilder = new ZugferdDocumentPdfBuilder($document, $basePdfPath);
        $pdfBuilder->generateDocument()->saveDocument($mergedPdfPath);

        return $mergedPdfPath;
    }

    public function render()
    {
        $customerId = Auth::guard('customer')->id();

        $invoices = ModInvoice::with('recipient', 'creator', 'items')
            ->where('customer_id', $customerId) // 👈 statt user_id
            ->get();

        return view('livewire.backend.e-invoice.invoice-pdf-manager', compact('invoices'))
                    ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
