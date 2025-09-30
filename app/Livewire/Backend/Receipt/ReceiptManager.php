<?php

namespace App\Livewire\Backend\Receipt;

use NumberFormatter;
use Livewire\Component;
use App\Models\ModReceipt;
use App\Models\ReceiptTemplate;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DataQuittungText;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReceiptManager extends Component
{
    use WithPagination;

    public $amount, $date, $description, $includeTax = false, $taxPercent = 19;
    public $showForm = false;
    public $editMode = false;
    public $receiptId;
    public $suggestedDescriptions = [];
    public $taxType = 'netto';
    public $customTaxPercent;
    public $receiptType = 'quittung'; // quittung, mietquittung, rechnung, gutschrift
    public $title = '';

    // Absender Felder
    public $sender, $sender_street, $sender_house_number, $sender_zip, $sender_city, $sender_phone, $sender_email, $sender_tax_number;

    // Empfänger Felder
    public $receiver, $receiver_street, $receiver_house_number, $receiver_zip, $receiver_city, $receiver_phone, $receiver_email;

    // Template Management
    public $templates = [];
    public $selectedTemplate = null;
    public $saveAsTemplate = false;
    public $templateName = '';
    public $templateType = 'quittung';

    // Verfügbare Quittungstypen
    public $receiptTypes = [
        'quittung' => 'Allgemeine Quittung',
        'mietquittung' => 'Mietquittung',
        'rechnung' => 'Rechnung',
        'gutschrift' => 'Gutschrift',
        'spendenquittung' => 'Spendenquittung',
        'kassenquittung' => 'Kassenquittung'
    ];

    protected $rules = [
        'receiptType' => 'required|string',
        'title' => 'nullable|string|max:255',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'description' => 'nullable|string|max:1000',
        'sender' => 'required|string|max:255',
        'sender_street' => 'nullable|string|max:255',
        'sender_house_number' => 'nullable|string|max:10',
        'sender_zip' => 'nullable|string|max:10',
        'sender_city' => 'nullable|string|max:255',
        'sender_phone' => 'nullable|string|max:20',
        'sender_email' => 'nullable|email|max:255',
        'sender_tax_number' => 'nullable|string|max:50',
        'receiver' => 'required|string|max:255',
        'receiver_street' => 'nullable|string|max:255',
        'receiver_house_number' => 'nullable|string|max:10',
        'receiver_zip' => 'nullable|string|max:10',
        'receiver_city' => 'nullable|string|max:255',
        'receiver_phone' => 'nullable|string|max:20',
        'receiver_email' => 'nullable|email|max:255',
    ];

public $availableTemplates = [];
public $selectedTemplateForUse = null;

// In der mount() Methode
public function mount()
{
    $this->date = now()->format('Y-m-d');
    $this->loadCustomerData();
    $this->loadTemplates();
    $this->loadAvailableTemplates();
    $this->title = $this->getDefaultTitle($this->receiptType);
}

    private function loadCustomerData()
    {
        $customer = Auth::guard('customer')->user();

        // Absender-Daten mit Customer-Daten vorbelegen
        $this->sender = $customer->name;
        $this->sender_street = $customer->street;
        $this->sender_house_number = $customer->house_number;
        $this->sender_zip = $customer->zip;
        $this->sender_city = $customer->city;
        $this->sender_phone = $customer->phone;
        $this->sender_email = $customer->email;
    }

    private function loadTemplates()
    {
        $this->templates = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
    }


// In der Livewire Component zusätzliche Methoden
// Neue Methoden für Template-Management
public function deleteTemplate($templateId)
{
    $template = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
        ->find($templateId);

    if ($template) {
        $templateName = $template->name;
        $template->delete();

        // Templates neu laden
        $this->loadTemplates();
        $this->loadAvailableTemplates();

        // Falls die gelöschte Vorlage aktuell ausgewählt war, zurücksetzen
        if ($this->selectedTemplateForUse == $templateId) {
            $this->selectedTemplateForUse = null;
        }
        if ($this->selectedTemplate == $templateId) {
            $this->selectedTemplate = null;
        }

        session()->flash('success', "Vorlage '{$templateName}' wurde gelöscht.");
    } else {
        session()->flash('error', 'Vorlage nicht gefunden.');
    }
}



// Neue Methode für Template-Auswahl
private function loadAvailableTemplates()
{
    $this->availableTemplates = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
        ->orderBy('name')
        ->get()
        ->mapWithKeys(function ($template) {
            return [
                $template->id => $template->name . ' (' . $this->receiptTypes[$template->type] . ')'
            ];
        })
        ->toArray();
}


// Methode um Vorlage zu laden
public function loadTemplateData()
{
    if ($this->selectedTemplateForUse) {
        $template = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
            ->find($this->selectedTemplateForUse);

        if ($template) {
            $this->sender = $template->sender_name ?? '';
            $this->sender_street = $template->sender_street ?? '';
            $this->sender_house_number = $template->sender_house_number ?? '';
            $this->sender_zip = $template->sender_zip ?? '';
            $this->sender_city = $template->sender_city ?? '';
            $this->sender_phone = $template->sender_phone ?? '';
            $this->sender_email = $template->sender_email ?? '';
            $this->sender_tax_number = $template->sender_tax_number ?? '';
            $this->receiver = $template->receiver_name ?? '';
            $this->receiver_street = $template->receiver_street ?? '';
            $this->receiver_house_number = $template->receiver_house_number ?? '';
            $this->receiver_zip = $template->receiver_zip ?? '';
            $this->receiver_city = $template->receiver_city ?? '';
            $this->receiver_phone = $template->receiver_phone ?? '';
            $this->receiver_email = $template->receiver_email ?? '';
            $this->includeTax = $template->include_tax ?? false;
            $this->taxPercent = $template->tax_percent ?? 19;
            $this->receiptType = $template->type ?? 'quittung';
            $this->description = $template->default_description ?? '';
            $this->templateType = $template->type ?? 'quittung';
            $this->title = $this->getDefaultTitle($this->receiptType);

            session()->flash('info', "Vorlage '{$template->name}' wurde geladen.");
        }
    }
}



public function setDefaultTemplate($templateId)
{
    // Alle Templates zurücksetzen
    ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
        ->update(['is_default' => false]);

    // Gewähltes Template als Standard setzen
    $template = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
        ->find($templateId);

    if ($template) {
        $template->update(['is_default' => true]);
        $this->loadTemplates();
        $this->loadAvailableTemplates();
        session()->flash('success', "Vorlage '{$template->name}' ist jetzt die Standardvorlage.");
    }
}

// In der Livewire Component - füge diese Methode hinzu
public function loadTemplateById($templateId)
{
    $this->selectedTemplateForUse = $templateId;
    $this->loadTemplateData();

    // Formular öffnen falls geschlossen
    if (!$this->showForm) {
        $this->showForm = true;
    }

    // Scroll zum Formular (optional)
    $this->dispatch('scroll-to-form');
}

    public function updatedSelectedTemplate($templateId)
    {
        if ($templateId) {
            $template = ReceiptTemplate::where('customer_id', Auth::guard('customer')->id())
                ->find($templateId);

            if ($template) {
                $this->sender = $template->sender_name;
                $this->sender_street = $template->sender_street;
                $this->sender_house_number = $template->sender_house_number;
                $this->sender_zip = $template->sender_zip;
                $this->sender_city = $template->sender_city;
                $this->sender_phone = $template->sender_phone;
                $this->sender_email = $template->sender_email;
                $this->sender_tax_number = $template->sender_tax_number;
                $this->receiver = $template->receiver_name;
                $this->receiver_street = $template->receiver_street;
                $this->receiver_house_number = $template->receiver_house_number;
                $this->receiver_zip = $template->receiver_zip;
                $this->receiver_city = $template->receiver_city;
                $this->receiver_phone = $template->receiver_phone;
                $this->receiver_email = $template->receiver_email;
                $this->includeTax = $template->include_tax;
                $this->taxPercent = $template->tax_percent ?? 19;
                $this->receiptType = $template->type;
                $this->description = $template->default_description;
            }
        }
    }


    private function getDefaultTitle($type)
    {
        return match($type) {
            'mietquittung' => 'Mietquittung',
            'rechnung' => 'Rechnung',
            'gutschrift' => 'Gutschrift',
            'spendenquittung' => 'Spendenquittung',
            'kassenquittung' => 'Kassenquittung',
            default => 'Quittung'
        };
    }

    public function updatedReceiptType($type)
    {
        // Titel automatisch basierend auf Typ setzen
        $this->title = $this->getDefaultTitle($type);
    }

public function updatedDescription()
{
    if (!empty($this->description)) {
        $this->suggestedDescriptions = DataQuittungText::where('customer_id', Auth::guard('customer')->id())
            ->where('text', 'like', '%' . $this->description . '%')
            ->pluck('text')
            ->toArray();
    } else {
        $this->suggestedDescriptions = [];
    }
}


    public function saveReceipt()
    {


    // DEBUG: Vor der Validierung
    logger('Form Data vor Validierung:', [
        'receiptType' => $this->receiptType,
        'title' => $this->title,
        'amount' => $this->amount,
        'date' => $this->date,
        'sender' => $this->sender,
        'receiver' => $this->receiver,
        'saveAsTemplate' => $this->saveAsTemplate,
        'templateName' => $this->templateName
    ]);

        $this->validate();

        $customerId = Auth::guard('customer')->id();
        $taxPercent = $this->customTaxPercent ?? $this->taxPercent;
        $taxAmount = 0;
        $netAmount = $this->amount;
        $grossAmount = $this->amount;

        if ($this->includeTax) {
            if ($this->taxType === 'netto') {
                $taxAmount = $this->amount * $taxPercent / 100;
                $grossAmount = $this->amount + $taxAmount;
            } elseif ($this->taxType === 'brutto') {
                $taxAmount = $this->amount * $taxPercent / (100 + $taxPercent);
                $netAmount = $this->amount - $taxAmount;
            }
        }

        $amountInWords = $this->convertNumberToWords($grossAmount);

        // Template speichern falls gewünscht
        $templateId = null;
        if ($this->saveAsTemplate && $this->templateName) {
            $template = ReceiptTemplate::create([
                'customer_id' => $customerId,
                'name' => $this->templateName,
                'type' => $this->templateType,
                'sender_name' => $this->sender,
                'sender_street' => $this->sender_street,
                'sender_house_number' => $this->sender_house_number,
                'sender_zip' => $this->sender_zip,
                'sender_city' => $this->sender_city,
                'sender_phone' => $this->sender_phone,
                'sender_email' => $this->sender_email,
                'sender_tax_number' => $this->sender_tax_number,
                'receiver_name' => $this->receiver,
                'receiver_street' => $this->receiver_street,
                'receiver_house_number' => $this->receiver_house_number,
                'receiver_zip' => $this->receiver_zip,
                'receiver_city' => $this->receiver_city,
                'receiver_phone' => $this->receiver_phone,
                'receiver_email' => $this->receiver_email,
                'default_description' => $this->description,
                'include_tax' => $this->includeTax,
                'tax_percent' => $taxPercent,
            ]);
            $templateId = $template->id;
            $this->loadTemplates();
        }

        if ($this->editMode) {
            $receipt = ModReceipt::where('customer_id', $customerId)
                ->findOrFail($this->receiptId);

            $receipt->update([
                'customer_id' => $customerId,
                'template_id' => $templateId,
                'receipt_type' => $this->receiptType,
                'title' => $this->title,
                'amount' => $netAmount,
                'date' => $this->date,
                'description' => $this->description,
                'sender' => $this->sender,
                'sender_street' => $this->sender_street,
                'sender_house_number' => $this->sender_house_number,
                'sender_zip' => $this->sender_zip,
                'sender_city' => $this->sender_city,
                'sender_phone' => $this->sender_phone,
                'sender_email' => $this->sender_email,
                'sender_tax_number' => $this->sender_tax_number,
                'receiver' => $this->receiver,
                'receiver_street' => $this->receiver_street,
                'receiver_house_number' => $this->receiver_house_number,
                'receiver_zip' => $this->receiver_zip,
                'receiver_city' => $this->receiver_city,
                'receiver_phone' => $this->receiver_phone,
                'receiver_email' => $this->receiver_email,
                'tax_percent' => $this->includeTax ? $taxPercent : null,
                'tax_amount' => $taxAmount,
                'amount_in_words' => $amountInWords,
            ]);

            $receipt->update(['hash' => $this->generateReceiptHash($receipt)]);
            $this->generatePdf($receipt);

            session()->flash('success', $this->getSuccessMessage('aktualisiert'));

        } else {
            $receiptNumber = $this->generateReceiptNumber();

            $receipt = ModReceipt::create([
                'customer_id' => $customerId,
                'template_id' => $templateId,
                'receipt_type' => $this->receiptType,
                'title' => $this->title,
                'amount' => $netAmount,
                'date' => $this->date,
                'description' => $this->description,
                'sender' => $this->sender,
                'sender_street' => $this->sender_street,
                'sender_house_number' => $this->sender_house_number,
                'sender_zip' => $this->sender_zip,
                'sender_city' => $this->sender_city,
                'sender_phone' => $this->sender_phone,
                'sender_email' => $this->sender_email,
                'sender_tax_number' => $this->sender_tax_number,
                'receiver' => $this->receiver,
                'receiver_street' => $this->receiver_street,
                'receiver_house_number' => $this->receiver_house_number,
                'receiver_zip' => $this->receiver_zip,
                'receiver_city' => $this->receiver_city,
                'receiver_phone' => $this->receiver_phone,
                'receiver_email' => $this->receiver_email,
                'tax_percent' => $this->includeTax ? $taxPercent : null,
                'tax_amount' => $taxAmount,
                'number' => $receiptNumber,
                'type' => 'standard',
                'amount_in_words' => $amountInWords,
            ]);

            $receipt->update(['hash' => $this->generateReceiptHash($receipt)]);
            $this->generatePdf($receipt);
            $this->saveDescription($this->description);

            session()->flash('success', $this->getSuccessMessage('erstellt'));
        }

        $this->resetForm();
        $this->showForm = false;
    }

    private function convertNumberToWords($number)
    {
        $f = new NumberFormatter('de', \NumberFormatter::SPELLOUT);
        return ucfirst($f->format($number)) . ' Euro';
    }

    private function generateReceiptHash($receipt)
    {
        $data = $receipt->number . $receipt->date . $receipt->amount . Auth::guard('customer')->id();
        return hash('sha256', $data);
    }

public function generatePdf($receipt)
{
    // Customer-Ordner für PDFs im PUBLIC storage
    $customerFolder = 'receipts/' . Auth::guard('customer')->id();

    // Dateiname mit Quittungsnummer
    $fileName = $customerFolder . '/receipt_' . $receipt->number . '.pdf';

    // Sicherstellen, dass der Customer-Ordner existiert
    if (!Storage::disk('public')->exists($customerFolder)) {
        Storage::disk('public')->makeDirectory($customerFolder);
    }

    // Generiere das PDF mit den aktuellen Daten
    $pdfContent = Pdf::loadView('pdf.receipt', ['receipt' => $receipt])->output();

    // Speichere das PDF im PUBLIC storage
    Storage::disk('public')->put($fileName, $pdfContent);

    // Aktualisiere den Pfad in der Datenbank (ohne 'public/' Präfix)
    $receipt->update(['pdf_path' => $fileName]);
}





    private function getSuccessMessage($action)
    {
        $typeNames = [
            'quittung' => 'Quittung',
            'mietquittung' => 'Mietquittung',
            'rechnung' => 'Rechnung',
            'gutschrift' => 'Gutschrift',
            'spendenquittung' => 'Spendenquittung',
            'kassenquittung' => 'Kassenquittung'
        ];

        $typeName = $typeNames[$this->receiptType] ?? 'Dokument';
        return "{$typeName} erfolgreich {$action}!";
    }



    // FEHLENDE METHODE HINZUGEFÜGT
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

// In der resetForm() Methode
private function resetForm()
{
    $this->reset([
        'amount', 'date', 'description', 'includeTax', 'taxPercent',
        'editMode', 'receiptId', 'customTaxPercent', 'taxType',
        'receiptType', 'title', 'receiver', 'receiver_street',
        'receiver_house_number', 'receiver_zip', 'receiver_city',
        'receiver_phone', 'receiver_email', 'selectedTemplate',
        'saveAsTemplate', 'templateName', 'templateType',
        'selectedTemplateForUse' // Neu hinzufügen
    ]);
    $this->loadCustomerData();
    $this->title = $this->getDefaultTitle($this->receiptType);
}

public function sendWhatsApp($id)
{
    $receipt = ModReceipt::where('customer_id', Auth::guard('customer')->id())
        ->findOrFail($id);

    if ($receipt->pdf_path && Storage::disk('public')->exists($receipt->pdf_path)) {
        // PUBLIC storage URL verwenden
        $pdfUrl = Storage::disk('public')->url($receipt->pdf_path);
        $message = urlencode("Hier ist Ihre Mietquittung.");
        $whatsAppUrl = "https://wa.me/?text=$message%0A$pdfUrl";

        return redirect()->to($whatsAppUrl);
    }

    session()->flash('error', 'PDF nicht gefunden!');
}

    public function editReceipt($id)
    {
        $receipt = ModReceipt::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        $this->amount = $receipt->amount;
        $this->date = $receipt->date;
        $this->description = $receipt->description;

        // Absender-Daten
        $this->sender = $receipt->sender;
        $this->sender_street = $receipt->sender_street;
        $this->sender_house_number = $receipt->sender_house_number;
        $this->sender_zip = $receipt->sender_zip;
        $this->sender_city = $receipt->sender_city;
        $this->sender_phone = $receipt->sender_phone;
        $this->sender_email = $receipt->sender_email;
        $this->sender_tax_number = $receipt->sender_tax_number;

        // Empfänger-Daten
        $this->receiver = $receipt->receiver;
        $this->receiver_street = $receipt->receiver_street;
        $this->receiver_house_number = $receipt->receiver_house_number;
        $this->receiver_zip = $receipt->receiver_zip;
        $this->receiver_city = $receipt->receiver_city;
        $this->receiver_phone = $receipt->receiver_phone;
        $this->receiver_email = $receipt->receiver_email;

        $this->includeTax = $receipt->tax_percent > 0;
        $this->taxPercent = $receipt->tax_percent;
        $this->editMode = true;
        $this->receiptId = $id;
        $this->showForm = true;
        $this->selectedTemplate = $receipt->template_id;
    }

    public function deleteReceipt($id)
    {
        $receipt = ModReceipt::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        Storage::delete($receipt->pdf_path);
        $receipt->delete();
        session()->flash('success', 'Quittung erfolgreich gelöscht!');
    }

private function saveDescription($description)
{
    if ($description && !DataQuittungText::where('text', $description)
        ->where('customer_id', Auth::guard('customer')->id())->exists()) {

        DataQuittungText::create([
            'customer_id' => Auth::guard('customer')->id(), // customer_id verwenden
            'text' => $description,
        ]);
    }
}

public function deleteDescription($text)
{
    $deleted = DataQuittungText::where('customer_id', Auth::guard('customer')->id())
        ->where('text', $text)
        ->delete();

    if ($deleted) {
        session()->flash('success', 'Beschreibung erfolgreich gelöscht!');
    } else {
        session()->flash('error', 'Beschreibung konnte nicht gelöscht werden.');
    }

    $this->updatedDescription();
}

    private function generateReceiptNumber()
    {
        $datePart = now()->format('Ymd');
        $dailyCount = ModReceipt::where('customer_id', Auth::guard('customer')->id())
            ->whereDate('created_at', now())->count();
        $incrementPart = str_pad($dailyCount + 1, 3, '0', STR_PAD_LEFT);
        return 'MQ-' . $datePart . '-' . $incrementPart;
    }

    public function render()
    {
        $this->updatedDescription();

        return view('livewire.backend.receipt.receipt-manager', [
            'receipts' => ModReceipt::where('customer_id', Auth::guard('customer')->id())
                ->orderBy('date', 'desc')
                ->paginate(10),
        ])->extends('backend.customer.layouts.app')
          ->section('content');
    }
}
