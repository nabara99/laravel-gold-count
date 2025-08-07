<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Location;
use App\Models\Period;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionCrud extends Component
{
    use WithPagination;

    public $date, $location_id, $period_id, $qty, $price, $type, $note;
    public $transactionId;
    public $isEdit = false;
    public $locations = [];
    public $increase = false;

    public $searchNote = '';
    public $searchType = '';
    public $searchIncrease = '';

    public $filterLocation = '';
    public $filterPeriod = '';
    public $filterMonth = '';
    public $selectedMonth = '';

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'date' => 'required|date',
        'location_id' => 'required|exists:locations,id',
        'period_id' => 'required|exists:periods,id',
        'qty' => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',
        'type' => 'required|in:kredit,debit',
        'increase' => 'boolean',
        'note' => 'nullable|string|max:255',
    ];

    protected $updatesQueryString = ['filterLocation', 'filterPeriod', 'filterMonth', 'searchNote', 'searchType', 'searchIncrease'];

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->locations = Location::orderBy('name')->get();
        $this->selectedMonth = $this->filterMonth;
    }

    public function getFormPeriodsProperty()
    {
        if (!$this->location_id) return collect();
        return Period::where('location_id', $this->location_id)->orderByDesc('date_start')->get();
    }

    public function updatedLocationId($value)
    {
        $this->period_id = null;
    }

    public function getFilterPeriodsProperty()
    {
        if (!$this->filterLocation) return collect();
        return Period::where('location_id', $this->filterLocation)->orderByDesc('date_start')->get();
    }

    public function updatedFilterLocation($value)
    {
        $this->filterPeriod = '';
    }

    public function applyFilters()
    {
        $this->filterMonth = $this->selectedMonth;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filterLocation = '';
        $this->filterPeriod = '';
        $this->filterMonth = '';
        $this->selectedMonth = '';
        $this->searchNote = '';
        $this->searchType = '';
        $this->searchIncrease = '';
        $this->resetPage();
    }

    public function getMonthOptions()
    {
        $months = [];
        $currentYear = now()->year;
        $startYear = $currentYear; // Show last 2 years + current year

        for ($year = $startYear; $year <= $currentYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $monthValue = sprintf('%04d-%02d', $year, $month);
                $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');
                $months[$monthValue] = $monthLabel;
            }
        }

        return array_reverse($months, true); // Show newest first
    }

    public function store()
    {
        $this->validate();

        Transaction::create([
            'date' => $this->date,
            'location_id' => $this->location_id,
            'period_id' => $this->period_id,
            'qty' => $this->qty,
            'price' => $this->price,
            'type' => $this->type,
            'increase' => $this->increase,
            'amount' => $this->qty * $this->price,
            'note' => $this->note,
        ]);

        $this->resetForm();
        $this->dispatch('toast:success', message: 'Transaksi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->isEdit = true;
        $trx = Transaction::findOrFail($id);
        $this->transactionId = $trx->id;
        $this->date = $trx->date;
        $this->location_id = $trx->location_id;
        $this->period_id = $trx->period_id;
        $this->qty = $trx->qty;
        $this->price = $trx->price;
        $this->type = $trx->type;
        $this->increase = $trx->increase;
        $this->note = $trx->note;
    }

    public function update()
    {
        $this->validate();

        Transaction::where('id', $this->transactionId)->update([
            'date' => $this->date,
            'location_id' => $this->location_id,
            'period_id' => $this->period_id,
            'qty' => $this->qty,
            'price' => $this->price,
            'type' => $this->type,
            'increase' => $this->increase,
            'amount' => $this->qty * $this->price,
            'note' => $this->note,
        ]);

        $this->resetForm();
        $this->dispatch('toast:success', message: 'Transaksi berhasil diupdate');
    }

    public function destroy($id)
    {
        Transaction::destroy($id);
        $this->dispatch('toast:success', message: 'Transaksi berhasil dihapus');
    }

    public function resetForm()
    {
        $this->reset(['date', 'location_id', 'period_id', 'qty', 'price', 'type', 'increase', 'transactionId', 'isEdit', 'note']);
        $this->date = now()->toDateString();
    }

    public function render()
    {
        $query = Transaction::with(['location', 'period'])
            ->when($this->filterLocation, fn($q) => $q->where('location_id', $this->filterLocation))
            ->when($this->filterPeriod, fn($q) => $q->where('period_id', $this->filterPeriod))
            ->when($this->filterMonth !== '', function ($q) {
                $q->whereYear('date', substr($this->filterMonth, 0, 4))
                  ->whereMonth('date', substr($this->filterMonth, 5, 2));
            })
            ->when($this->searchNote, fn($q) => $q->where('note', 'like', '%' . $this->searchNote . '%'))
            ->when($this->searchType, fn($q) => $q->where('type', $this->searchType))
            ->when($this->searchIncrease !== '', fn($q) => $q->where('increase', $this->searchIncrease))
            ->orderByDesc('date');

        $transactions = $query->paginate(10);

        $totalKredit = $query->clone()->where('type', 'kredit')->sum('amount');
        $totalDebit = $query->clone()->where('type', 'debit')->where('increase', '1')->sum('amount');
        $totalQtyKredit = $query->clone()->where('type', 'kredit')->sum('qty');
        $net = $totalKredit - $totalDebit;

        $percentWorker = 30;
        $percentInvestor = 70;

        if ($this->filterLocation) {
            $location = \App\Models\Location::find($this->filterLocation);
            if ($location) {
                $percentWorker = $location->percent_worker;
                $percentInvestor = $location->percent_investor;
            }
        }

        return view('livewire.transaction-crud', [
            'transactions' => $transactions,
            'totalKredit' => $totalKredit,
            'totalDebit' => $totalDebit,
            'totalQtyKredit' => $totalQtyKredit,
            'net' => $net,
            'toWorkers' => $net * ($percentWorker / 100),
            'toInvestors' => $net * ($percentInvestor / 100),
            'formPeriods' => $this->formPeriods,
            'filterPeriods' => $this->filterPeriods,
            'monthOptions' => $this->getMonthOptions(),
            'percentWorker' => $percentWorker,
            'percentInvestor' => $percentInvestor,
        ]);
    }
}
