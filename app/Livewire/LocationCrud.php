<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;

class LocationCrud extends Component
{
    public $locations, $name, $address, $locationId;
    public $isEdit = false;
    public $percent_investor = 0, $percent_worker = 0;


    protected $rules = [
        'name' => 'required|unique:locations,name',
        'address' => 'nullable|string',
        'percent_investor' => 'required|integer|min:0|max:100',
        'percent_worker' => 'required|integer|min:0|max:100',
    ];

    public function render()
    {
        $this->locations = Location::orderBy('created_at', 'desc')->get();
        return view('livewire.location-crud');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->address = '';
        $this->percent_investor = 0;
        $this->percent_worker = 0;
        $this->locationId = null;
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function store()
    {
        if (($this->percent_investor + $this->percent_worker) !== 100) {
            $this->addError('percent_worker', 'Total pembagian harus 100%.');
            return;
        }

        $this->validate();

        Location::create([
            'name' => $this->name,
            'address' => $this->address,
            'percent_investor' => $this->percent_investor,
            'percent_worker' => $this->percent_worker
        ]);

        $this->dispatch('toast:success', message: 'Data lokasi berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $lokasi = Location::findOrFail($id);
        $this->name = $lokasi->name;
        $this->address = $lokasi->address;
        $this->percent_investor = $lokasi->percent_investor;
        $this->percent_worker = $lokasi->percent_worker;
        $this->locationId = $lokasi->id;
        $this->isEdit = true;
    }

    public function update()
    {
        if (($this->percent_investor + $this->percent_worker) !== 100) {
            $this->addError('percent_worker', 'Total pembagian harus 100%.');
            return;
        }

        $this->validate([
            'name' => 'required|unique:locations,name,' . $this->locationId,
            'address' => 'nullable|string',
            'percent_investor' => 'required|integer|min:0|max:100',
            'percent_worker' => 'required|integer|min:0|max:100',
        ]);

        Location::find($this->locationId)->update([
            'name' => $this->name,
            'address' => $this->address,
            'percent_investor' => $this->percent_investor,
            'percent_worker' => $this->percent_worker
        ]);

        $this->dispatch('toast:success', message: 'Data lokasi berhasil diperbaharui.');

        $this->resetForm();
    }

    public function destroy($id)
    {
        Location::destroy($id);
        $this->dispatch('toast:success', message: 'Data lokasi berhasil dihapus.');
    }
}
