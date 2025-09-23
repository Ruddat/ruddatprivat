<?php

namespace App\Livewire\Backend\UtilityCosts;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UtilityCosts\RentalObject;
use App\Models\UtilityCosts\BillingHeader;
use App\Models\UtilityCosts\UtilityTenant;
use App\Models\UtilityCosts\BillingRecords;
use App\Services\UtilityCosts\BillingService;
// use PhpOffice\PhpSpreadsheet\Writer\Pdf;

class BillingGeneration extends Component
{
    use WithPagination;

    public $billingHeaders;
    public $tenants;
    public $rentalObjects;
    public $billing_header_id;
    public $tenant_id;
    public $billing_period;
    public $savedBillings;

    // Fehlende Eigenschaften hinzufügen
    public $selectedHeaderId;
    public $selectedTenantId;
    public $selectedRentalObjectId;
    public $billingPeriod;
    public $prepayment;

    public $searchTerm; // Für die Suchfunktion
    public $fromDate;
    public $toDate;
    public $sortField = 'billing_records.created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['searchTerm', 'fromDate', 'toDate', 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->billingHeaders = BillingHeader::where('user_id', Auth::guard('customer')->id())->get();
        $this->tenants = UtilityTenant::where('user_id', Auth::guard('customer')->id())->get();
        $this->rentalObjects = RentalObject::where('user_id', Auth::guard('customer')->id())->get();
        $this->savedBillings = BillingRecords::where('user_id', Auth::guard('customer')->id())->get();
    }

public function generateBilling()
{
    $this->validate([
        'selectedHeaderId' => 'required|exists:billing_headers,id',
        'selectedRentalObjectId' => 'required|exists:rental_objects,id',
        'billingPeriod' => 'required|string',
        'prepayment' => 'nullable|numeric|min:0',
    ]);

    $year = substr($this->billingPeriod, 0, 4);

// dd($this->selectedTenantId, $this->selectedRentalObjectId, $this->billingPeriod, $year);


    // 🔥 Wenn Mieter gewählt -> nur diesen, sonst alle im Objekt
    if ($this->selectedTenantId) {
        $tenants = UtilityTenant::where('id', $this->selectedTenantId)
            ->where('user_id', Auth::guard('customer')->id())
            ->get();
    } else {
        $tenants = UtilityTenant::where('rental_object_id', $this->selectedRentalObjectId)
            ->where('user_id', Auth::guard('customer')->id())
            ->get();
    }

    foreach ($tenants as $tenant) {
        // Vorauszahlungen holen
        $prepaymentSum = DB::table('tenant_payments')
            ->where('tenant_id', $tenant->id)
            ->where('rental_object_id', $this->selectedRentalObjectId)
            ->where('year', $year)
            ->where('user_id', Auth::guard('customer')->id())
            ->sum('amount');

        // Kosten berechnen
        $billingService = app(BillingService::class);
        $calculation = $billingService->calculateCosts(
            $this->selectedRentalObjectId,
            $tenant->id,
            $this->billingPeriod,
            $prepaymentSum
        );

        // Heizkosten sammeln
        $heatingCosts = DB::table('heating_costs')
            ->where('rental_object_id', $this->selectedRentalObjectId)
            ->where('year', $year)
            ->where('user_id', Auth::guard('customer')->id())
            ->get();

        $totalInitialReading = 0;
        $totalFinalReading = 0;
        $totalFuelConsumption = 0;
        $totalFuelCost = 0;
        $totalWarmWaterCost = 0;
        $totalHeatingOnlyCost = 0;

        foreach ($heatingCosts as $cost) {
            $fuelCost = ($cost->total_oil_used ?? 0) * ($cost->price_per_unit ?? 0);
            $warmWaterCost = $fuelCost * ($cost->warm_water_percentage ?? 0);
            $heatingOnlyCost = $fuelCost * (1 - ($cost->warm_water_percentage ?? 0));

            $totalInitialReading += $cost->initial_reading ?? 0;
            $totalFinalReading += $cost->final_reading ?? 0;
            $totalFuelConsumption += $cost->total_oil_used ?? 0;
            $totalFuelCost += $fuelCost;
            $totalWarmWaterCost += $warmWaterCost;
            $totalHeatingOnlyCost += $heatingOnlyCost;
        }

        $totalWarmWaterPercentage = $totalFuelCost > 0
            ? ($totalWarmWaterCost / $totalFuelCost) * 100
            : 0;

        $heatingData = [
            'totalInitialReading' => $totalInitialReading,
            'totalFinalReading' => $totalFinalReading,
            'totalFuelConsumption' => $totalFuelConsumption,
            'totalFuelCost' => $totalFuelCost,
            'totalWarmWaterCost' => $totalWarmWaterCost,
            'totalHeatingOnlyCost' => $totalHeatingOnlyCost,
            'warmWaterPercentage' => $totalWarmWaterPercentage,
        ];

        // BillingRecord speichern
        $billingRecord = BillingRecords::create([
            'user_id' => Auth::guard('customer')->id(),
            'billing_header_id' => $this->selectedHeaderId,
            'tenant_id' => $tenant->id,
            'rental_object_id' => $this->selectedRentalObjectId,
            'billing_period' => $this->billingPeriod,
            'total_cost' => $calculation['total_cost'],
            'prepayment' => $prepaymentSum,
            'balance_due' => $calculation['balance_due'],
            'standard_costs' => json_encode($calculation['standard_costs']),
            'heating_costs' => json_encode($calculation['heating_costs']),
        ]);

        // Zahlungen holen
        $tenantPayments = DB::table('tenant_payments')
            ->where('tenant_id', $tenant->id)
            ->where('rental_object_id', $this->selectedRentalObjectId)
            ->where('year', $year)
            ->where('user_id', Auth::guard('customer')->id())
            ->get();

        // Refunds / Payments holen
        $refundsOrPayments = DB::table('refunds_or_payments')
            ->where('tenant_id', $tenant->id)
            ->where('rental_object_id', $this->selectedRentalObjectId)
            ->where('year', $year)
            ->where('user_id', Auth::guard('customer')->id())
            ->get();

        $totalPayments = $refundsOrPayments->where('type', 'payment')->sum('amount');
        $totalRefunds = $refundsOrPayments->where('type', 'refund')->sum('amount');

        $adjustedBalance = $billingRecord->total_cost
            - $billingRecord->prepayment
            - $totalPayments
            + $totalRefunds;

        $billingRecord->update([
            'balance_due' => $adjustedBalance,
        ]);

        // PDF-Daten
        $pdfData = [
            'billingRecord' => $billingRecord,
            'billingHeader' => $billingRecord->billingHeader,
            'tenant' => $tenant,
            'rentalObject' => $billingRecord->rentalObject,
            'tenants' => UtilityTenant::where('rental_object_id', $this->selectedRentalObjectId)
                ->where('user_id', Auth::guard('customer')->id())
                ->get(),
            'billingPeriod' => $this->billingPeriod,
            'heatingData' => $heatingData,
            'heatingCosts' => $heatingCosts,
            'calculation' => $calculation,
            'tenantPayments' => $tenantPayments,
            'refundsOrPayments' => $refundsOrPayments,
        ];

        // PDFs erzeugen
        $this->generatePdf($pdfData, 'billing', 'billing_page1_', $billingRecord, 'pdf_path');
        $this->generatePdf($pdfData, 'billing_page2', 'billing_page2_', $billingRecord, 'pdf_path_second');
        $this->generatePdf($pdfData, 'tenant_payments', 'billing_page3_', $billingRecord, 'pdf_path_third');
    }

    session()->flash('message', $this->selectedTenantId
        ? 'Abrechnung für den gewählten Mieter erstellt.'
        : 'Abrechnungen für alle Mieter des Objekts erstellt.'
    );

    $this->savedBillings = BillingRecords::where('user_id', Auth::guard('customer')->id())->get();
}

    private function generatePdf($data, $view, $filePrefix, $billingRecord, $pathField)
    {
        $pdf = Pdf::loadView("pdf.$view", $data)
                  ->setPaper('a4')
                  ->setOptions(['margin-top' => 10, 'margin-right' => 20, 'margin-bottom' => 10, 'margin-left' => 20]);
        $filePath = "billing_pdfs/{$filePrefix}" . now()->timestamp . ".pdf";
        Storage::disk('public')->put($filePath, $pdf->output());

        $billingRecord->update([$pathField => Storage::url($filePath)]);
    }

    public function deleteBilling($id)
    {
        $billingRecord = BillingRecords::where('user_id', Auth::guard('customer')->id())->find($id);

        if ($billingRecord) {
            if ($billingRecord->pdf_path && Storage::disk('public')->exists($billingRecord->pdf_path)) {
                Storage::disk('public')->delete($billingRecord->pdf_path);
            }

            $billingRecord->delete();

            session()->flash('message', 'Abrechnung erfolgreich gelöscht.');
            $this->savedBillings = BillingRecords::where('user_id', Auth::guard('customer')->id())->get();
        } else {
            session()->flash('error', 'Abrechnung konnte nicht gefunden werden.');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->fromDate = null;
        $this->toDate = null;
    }


public function render()
{
    $query = BillingRecords::query()
        ->with(['billingHeader', 'tenant', 'rentalObject'])
        ->leftJoin('utility_tenants', 'billing_records.tenant_id', '=', 'utility_tenants.id')
        ->leftJoin('billing_headers', 'billing_records.billing_header_id', '=', 'billing_headers.id')
        ->select(
            'billing_records.*',
            'utility_tenants.first_name as tenant_first_name',
            'utility_tenants.last_name as tenant_last_name',
            'billing_headers.creator_name as billing_header_creator_name'
        )
        ->where('billing_records.user_id', Auth::guard('customer')->id());

    // 🔎 Suche: Vorname, Nachname, "Vorname Nachname", Header-Name, Zeitraum
    if (filled($this->searchTerm)) {
        $s = '%' . trim($this->searchTerm) . '%';
        $query->where(function ($q) use ($s) {
            $q->where('utility_tenants.first_name', 'like', $s)
              ->orWhere('utility_tenants.last_name', 'like', $s)
              ->orWhere(DB::raw("CONCAT(utility_tenants.first_name, ' ', utility_tenants.last_name)"), 'like', $s)
              ->orWhere('billing_headers.creator_name', 'like', $s)
              ->orWhere('billing_records.billing_period', 'like', $s);
        });
    }

    // 📅 Datum: einzeln oder beide
    if ($this->fromDate && $this->toDate) {
        $query->whereBetween('billing_records.created_at', [
            Carbon::parse($this->fromDate)->startOfDay(),
            Carbon::parse($this->toDate)->endOfDay(),
        ]);
    } elseif ($this->fromDate) {
        $query->where('billing_records.created_at', '>=', Carbon::parse($this->fromDate)->startOfDay());
    } elseif ($this->toDate) {
        $query->where('billing_records.created_at', '<=', Carbon::parse($this->toDate)->endOfDay());
    }

    // 🔽 Sorting-Whitelist
    $sortable = [
        'billing_records.created_at',
        'billing_period',
        'tenant_first_name',
        'billing_header_creator_name',
    ];
    $field = in_array($this->sortField, $sortable, true) ? $this->sortField : 'billing_records.created_at';
    $dir   = $this->sortDirection === 'asc' ? 'asc' : 'desc';
    $query->orderBy($field, $dir);

    $savedBillings = $query->paginate(10);

    return view('livewire.backend.utility-costs.billing-generation', [
        'billingHeaders' => $this->billingHeaders,
        'tenants'        => $this->tenants,
        'rentalObjects'  => $this->rentalObjects,
        'savedBillings'  => $savedBillings,
    ])->extends('backend.customer.layouts.app')->section('content');
}

}
