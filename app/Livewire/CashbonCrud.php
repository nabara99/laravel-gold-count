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

    protected $rules = [
        'worker_id' => 'required',
        'location_id' => 'required',
        'date' => 'required|date',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|in:paid,unpaid',
        'description' => 'nullable|string',
    ];

    public function render()
    {
        return view('livewire.cashbon-crud', [
            'cashbons' => Cashbon::with(['worker', 'location'])->latest()->paginate(10),
            'workers' => Worker::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
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

    public function resetInput()
    {
        $this->reset(['cashbonId', 'worker_id', 'location_id', 'date', 'amount', 'description', 'status', 'isEdit']);
    }
}
