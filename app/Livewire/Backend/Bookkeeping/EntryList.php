<?php

namespace App\Livewire\Backend\Bookkeeping;

use App\Models\Entry;
use App\Models\FiscalYear;
use App\Models\Tenant;
use App\Models\BkReceipt;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EntryList extends Component
{
    use WithPagination;

    public $tenantId;
    public $availableTenants;
    public $yearId;
    public $search = '';

    protected $queryString = ['yearId', 'search', 'page'];

    public function mount()
    {
        try {
            // Alle Mandanten des Kunden holen
            $this->availableTenants = Tenant::where('customer_id', Auth::guard('customer')->id())
                ->orderBy('name')
                ->get();

            if ($this->availableTenants->isEmpty()) {
                Log::warning('Keine Mandanten gefunden für Customer: ' . Auth::guard('customer')->id());
                return;
            }

            // Aktuellen Mandanten finden oder ersten nehmen
            $currentTenant = $this->availableTenants->where('is_current', true)->first();
            $firstTenant = $this->availableTenants->first();

            $this->tenantId = $currentTenant ? $currentTenant->id : ($firstTenant ? $firstTenant->id : null);

            // Fallback: Wenn kein Tenant gesetzt werden konnte, ersten nehmen
            if (!$this->tenantId) {
                $this->tenantId = $this->availableTenants->first()->id;
                Log::warning('Fallback: Ersten Mandanten verwendet für EntryList');
            }

        } catch (\Exception $e) {
            Log::error('Fehler in EntryList mount(): ' . $e->getMessage());
            session()->flash('error', 'Fehler beim Laden der Mandanten: ' . $e->getMessage());
        }
    }

    public function updatedTenantId($value)
    {
        try {
            $this->tenantId = $value;
            $this->resetPage();

            // Jahr zurücksetzen wenn neuer Tenant gewechselt wird
            $this->yearId = null;

        } catch (\Exception $e) {
            Log::error('Fehler beim Wechseln des Mandanten: ' . $e->getMessage());
            session()->flash('error', 'Fehler beim Wechseln des Mandanten: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingYearId()
    {
        $this->resetPage();
    }

    public function deleteEntry($entryId)
    {
        try {
            $entry = Entry::where('tenant_id', $this->tenantId)
                        ->find($entryId);

            if (!$entry) {
                session()->flash('error', 'Buchung nicht gefunden oder Zugriff verweigert.');
                return;
            }

            // Prüfen ob es sich um eine Transaktion mit mehreren Buchungssätzen handelt
            $relatedEntries = Entry::where('transaction_id', $entry->transaction_id)
                ->where('tenant_id', $this->tenantId)
                ->get();

            if ($relatedEntries->count() > 1) {
                // Alle Buchungssätze der Transaktion löschen
                Entry::where('transaction_id', $entry->transaction_id)
                    ->where('tenant_id', $this->tenantId)
                    ->delete();

                session()->flash('success', 'Komplette Buchungstransaktion wurde gelöscht.');
            } else {
                // Einzelnen Buchungssatz löschen
                $entry->delete();
                session()->flash('success', 'Buchung wurde gelöscht.');
            }

        } catch (\Exception $e) {
            Log::error('Fehler beim Löschen der Buchung: ' . $e->getMessage());
            session()->flash('error', 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function downloadReceipt($receiptId)
    {
        try {
            $receipt = BkReceipt::where('tenant_id', $this->tenantId)
                ->findOrFail($receiptId);

            if (!$receipt->file_path || !Storage::disk('public')->exists($receipt->file_path)) {
                session()->flash('error', 'Beleg-Datei nicht gefunden.');
                return;
            }

            return Storage::disk('public')->download($receipt->file_path,
                'beleg-' . ($receipt->number ?? $receipt->id) . '.pdf');

        } catch (\Exception $e) {
            Log::error('Fehler beim Download des Belegs: ' . $e->getMessage());
            session()->flash('error', 'Fehler beim Download: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $years = collect();
        $transactions = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $paginatedEntries = null;

        try {
            if (!$this->tenantId) {
                return $this->viewWithError('Bitte wählen Sie einen Mandanten aus.');
            }

            // Geschäftsjahre für aktuellen Mandanten laden
            $years = FiscalYear::where('tenant_id', $this->tenantId)
                ->orderBy('year', 'desc')
                ->get();

            // Query für Buchungen
            $query = Entry::with([
                'debitAccount',
                'creditAccount',
                'receipt',
                'fiscalYear'
            ])->where('tenant_id', $this->tenantId);

            // Geschäftsjahr Filter
            if ($this->yearId) {
                $query->where('fiscal_year_id', $this->yearId);
            }

            // Such-Filter
            if ($this->search) {
                $searchTerm = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('description', 'like', $searchTerm)
                      ->orWhereHas('debitAccount', function($q2) use ($searchTerm) {
                          $q2->where('name', 'like', $searchTerm)
                             ->orWhere('number', 'like', $searchTerm);
                      })
                      ->orWhereHas('creditAccount', function($q2) use ($searchTerm) {
                          $q2->where('name', 'like', $searchTerm)
                             ->orWhere('number', 'like', $searchTerm);
                      });

                    // Datum-Suche (verschiedene Formate unterstützen)
                    if (preg_match('/\d{4}-\d{2}-\d{2}/', $this->search)) {
                        $q->orWhereDate('booking_date', $this->search);
                    } elseif (preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->search)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d.m.Y', $this->search)->format('Y-m-d');
                            $q->orWhereDate('booking_date', $date);
                        } catch (\Exception $e) {
                            // Ungültiges Datum ignorieren
                        }
                    }
                });
            }

            // Paginierte Einträge für Pagination
            $paginatedEntries = $query
                ->orderBy('booking_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(50);

            // Gruppiere die Einträge nach transaction_id für die Anzeige
            $transactions = $paginatedEntries->groupBy('transaction_id');

            // Summen berechnen (nur für aktuelle Seite)
            $totalDebit = $paginatedEntries->sum(function($entry) {
                return $entry->debit_account_id ? $entry->amount : 0;
            });

            $totalCredit = $paginatedEntries->sum(function($entry) {
                return $entry->credit_account_id ? $entry->amount : 0;
            });

        } catch (\Exception $e) {
            Log::error('Fehler in EntryList render(): ' . $e->getMessage());
            return $this->viewWithError('Fehler beim Laden der Buchungen: ' . $e->getMessage());
        }

        $currentTenant = $this->availableTenants->firstWhere('id', $this->tenantId);

        return view('livewire.backend.bookkeeping.entry-list', [
            'paginatedEntries' => $paginatedEntries, // Für Pagination
            'transactions' => $transactions, // Für Gruppierte Anzeige
            'years' => $years,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'availableTenants' => $this->availableTenants,
            'currentTenant' => $currentTenant,
        ])->extends('backend.customer.layouts.app')->section('content');
    }

    protected function viewWithError($message)
    {
        return view('livewire.backend.bookkeeping.entry-list', [
            'paginatedEntries' => null,
            'transactions' => collect(),
            'years' => collect(),
            'totalDebit' => 0,
            'totalCredit' => 0,
            'availableTenants' => $this->availableTenants,
            'currentTenant' => null,
            'error' => $message,
        ])->extends('backend.customer.layouts.app')->section('content');
    }
}
