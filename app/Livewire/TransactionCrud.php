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

    public $filterLocation = '';
    public $filterPeriod = '';

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'date' => 'required|date',
        'location_id' => 'required|exists:locations,id',
        'period_id' => 'required|exists:periods,id',
        'qty' => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',
        'type' => 'required|in:kredit,debit',
        'note' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->locations = Location::orderBy('name')->get();
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
        $this->reset(['date', 'location_id', 'period_id', 'qty', 'price', 'type', 'transactionId', 'isEdit']);
    }

    public function render()
    {
        $query = Transaction::with(['location', 'period'])
            ->when($this->filterLocation, fn($q) => $q->where('location_id', $this->filterLocation))
            ->when($this->filterPeriod, fn($q) => $q->where('period_id', $this->filterPeriod))
            ->orderByDesc('date');

        $transactions = $query->paginate(10);

        $totalKredit = $query->clone()->where('type', 'kredit')->sum('amount');
        $totalDebit = $query->clone()->where('type', 'debit')->sum('amount');
        $net = $totalKredit - $totalDebit;

        return view('livewire.transaction-crud', [
            'transactions' => $transactions,
            'totalKredit' => $totalKredit,
            'totalDebit' => $totalDebit,
            'net' => $net,
            'toWorkers' => $net * 0.3,
            'toInvestors' => $net * 0.7,
            'formPeriods' => $this->formPeriods,
            'filterPeriods' => $this->filterPeriods,
        ]);
    }

}
