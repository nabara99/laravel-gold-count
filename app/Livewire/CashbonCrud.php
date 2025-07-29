<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cashbon;
use App\Models\Worker;
use App\Models\Location;
use Livewire\WithPagination;

class CashbonCrud extends Component
{
    use WithPagination;

    public $cashbonId, $worker_id, $location_id, $date, $amount, $description, $status = 'unpaid';
    public $isEdit = false;
    public $filterWorker = '';
    public $filterLocation = '';
    public $totalAmount = 0;

    protected $rules = [
        'worker_id' => 'required',
        'location_id' => 'required',
        'date' => 'required|date',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|in:paid,unpaid',
        'description' => 'nullable|string',
    ];

    public function applyFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Cashbon::with(['worker', 'location']);

        if ($this->filterWorker) {
            $query->where('worker_id', $this->filterWorker);
        }

        if ($this->filterLocation) {
            $query->where('location_id', $this->filterLocation);
        }

        $this->totalAmount = $query->sum('amount');

        return view('livewire.cashbon-crud', [
            'cashbons' => $query->latest()->paginate(10),
            'workers' => Worker::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    public function updatedFilterWorker()
    {
        $this->resetPage();
    }

    public function updatedFilterLocation()
    {
        $this->resetPage();
    }


    public function store()
    {
        $this->validate();

        Cashbon::create($this->only([
            'worker_id', 'location_id', 'date', 'amount', 'description', 'status'
        ]));

        $this->dispatch('toast:success', message: 'Kasbon berhasil disimpan.');
        $this->resetInput();
    }

    public function resetForm()
    {
        $this->reset(['cashbonId', 'worker_id', 'location_id', 'date', 'amount', 'description', 'status', 'isEdit']);
    }

    public function edit($id)
    {
        $cashbon = Cashbon::findOrFail($id);
        $this->fill($cashbon->toArray());
        $this->cashbonId = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        $cashbon = Cashbon::findOrFail($this->cashbonId);
        $cashbon->update($this->only([
            'worker_id', 'location_id', 'date', 'amount', 'description', 'status'
        ]));

        $this->dispatch('toast:success', message: 'Kasbon berhasil diupdate.');
        $this->resetInput();
    }

    public function delete($id)
    {
        Cashbon::destroy($id);
        $this->dispatch('toast:success', message: 'Kasbon berhasil dihapus.');
    }

    public function markAsPaid($id)
    {
        $cashbon = Cashbon::findOrFail($id);

        if ($cashbon->status === 'unpaid') {
            $cashbon->status = 'paid';
            $cashbon->save();

            session()->flash('success', 'Status berhasil diubah menjadi Paid.');
        }
    }

    public function markAsUnpaid($id)
    {
        $cashbon = Cashbon::findOrFail($id);

        if ($cashbon->status === 'paid') {
            $cashbon->status = 'unpaid';
            $cashbon->save();

            session()->flash('success', 'Status berhasil diubah menjadi Unpaid.');
        }
    }


    public function resetInput()
    {
        $this->reset(['cashbonId', 'worker_id', 'location_id', 'date', 'amount', 'description', 'status', 'isEdit']);
    }
}
