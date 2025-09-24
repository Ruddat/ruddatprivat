<?php

namespace App\Livewire\Backend\EInvoice;

use Livewire\Component;
use App\Models\ModInvoice;
use App\Models\ModInvoiceCreator;
use App\Models\ModInvoiceRecipient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class InvoiceManager extends Component
{
    public $invoices;
    public $recipients;
    public $invoice_creators;
    public $items = [];
    public $invoice = [];
    public $showForm = false;

    protected $rules = [
        'invoice.invoice_creator_id' => 'required|exists:mod_invoice_creators,id',
        'invoice.recipient_id'       => 'required|exists:mod_invoice_recipients,id',
        'invoice.invoice_date'       => 'required|date',
        'invoice.due_date'           => 'required|date',
        'invoice.notes'              => 'nullable|string',
        'invoice.status'             => 'required|in:draft,sent,paid,cancelled',

        'items.*.description' => 'required|string|max:255',
        'items.*.quantity'    => 'required|integer|min:1',
        'items.*.unit_price'  => 'required|numeric|min:0.01',
        'items.*.tax_rate'    => 'nullable|numeric|min:0|max:100',
    ];

    protected function validationAttributes()
    {
        return [
            'invoice.invoice_creator_id' => 'Rechnungskopf',
            'invoice.recipient_id'       => 'Kunde',
            'invoice.invoice_date'       => 'Rechnungsdatum',
            'invoice.due_date'           => 'Fälligkeitsdatum',
            'invoice.status'             => 'Status',
            'items.*.description'        => 'Beschreibung',
            'items.*.quantity'           => 'Menge',
            'items.*.unit_price'         => 'Einzelpreis',
            'items.*.tax_rate'           => 'Steuersatz',
        ];
    }

    public function mount()
    {
        if (!Auth::guard('customer')->check()) {
            abort(403, 'Bitte als Kunde anmelden.');
        }

        $customerId = Auth::guard('customer')->id();

        $this->invoices = ModInvoice::with('recipient', 'items')
            ->where('customer_id', $customerId)
            ->get();

        $this->recipients = ModInvoiceRecipient::where('customer_id', $customerId)->get();
        $this->invoice_creators = ModInvoiceCreator::where('customer_id', $customerId)->get();

        $this->items = [];
        $this->invoice = [
            'invoice_creator_id' => null,
            'recipient_id'       => null,
            'invoice_date'       => now()->toDateString(),
            'due_date'           => now()->addDays(30)->toDateString(),
            'status'             => 'draft',
        ];
    }

    public function addItem()
    {
        $this->items[] = [
            'item_number'  => '',
            'description'  => '',
            'quantity'     => 1,
            'unit_price'   => 0.00,
            'tax_rate'     => 0.00,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function saveInvoice()
    {
        $customerId = Auth::guard('customer')->id();

        if (empty($this->items)) {
            $this->addError('items', 'Die Rechnung muss mindestens eine Position enthalten.');
            return;
        }

        $this->validate();

        try {
            if (isset($this->invoice['id'])) {
                // Update
                $invoice = ModInvoice::where('id', $this->invoice['id'])
                    ->where('customer_id', $customerId)
                    ->firstOrFail();

                $invoice->update([
                    'recipient_id' => $this->invoice['recipient_id'],
                    'creator_id'   => $this->invoice['invoice_creator_id'],
                    'invoice_date' => $this->invoice['invoice_date'],
                    'due_date'     => $this->invoice['due_date'],
                    'status'       => $this->invoice['status'],
                    'notes'        => $this->invoice['notes'] ?? null,
                ]);

                $invoice->items()->delete();
            } else {
                // Create
                $invoice = ModInvoice::create([
                    'customer_id'    => $customerId,
                    'invoice_number' => 'INV-' . time(),
                    'recipient_id'   => $this->invoice['recipient_id'],
                    'creator_id'     => $this->invoice['invoice_creator_id'],
                    'invoice_date'   => $this->invoice['invoice_date'],
                    'due_date'       => $this->invoice['due_date'],
                    'total_amount'   => 0,
                    'status'         => $this->invoice['status'],
                    'notes'          => $this->invoice['notes'] ?? null,
                ]);
            }

            // Items speichern
            $totalAmount = 0;
            foreach ($this->items as $item) {
                $item['total_price'] = $item['quantity'] * $item['unit_price']
                    + ($item['quantity'] * $item['unit_price'] * ($item['tax_rate'] / 100));
                $invoice->items()->create($item);
                $totalAmount += $item['total_price'];
            }

            $invoice->update(['total_amount' => $totalAmount]);

            $this->invoices = ModInvoice::with('recipient', 'items')
                ->where('customer_id', $customerId)
                ->get();

            $this->reset(['invoice', 'items']);
            $this->showForm = false;

            session()->flash('message', isset($this->invoice['id'])
                ? 'Rechnung aktualisiert!'
                : 'Rechnung erstellt!');
        } catch (Exception $e) {
            logger()->error($e);
            session()->flash('error', 'Fehler beim Speichern der Rechnung.');
        }
    }

    public function editInvoice($id)
    {
        $customerId = Auth::guard('customer')->id();

        $invoice = ModInvoice::with('creator', 'items')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $this->invoice = [
            'id'                => $invoice->id,
            'invoice_creator_id'=> $invoice->creator_id,
            'recipient_id'      => $invoice->recipient_id,
            'invoice_date'      => $invoice->invoice_date,
            'due_date'          => $invoice->due_date,
            'status'            => $invoice->status,
            'notes'             => $invoice->notes,
        ];

        $this->items = $invoice->items->toArray();
        $this->showForm = true;
    }

    public function deleteInvoice($id)
    {
        $customerId = Auth::guard('customer')->id();

        $invoice = ModInvoice::where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $invoice->delete();

        $this->invoices = ModInvoice::with('recipient')
            ->where('customer_id', $customerId)
            ->get();

        session()->flash('message', 'Rechnung gelöscht!');
    }

    public function render()
    {
        return view('livewire.backend.e-invoice.invoice-manager')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
