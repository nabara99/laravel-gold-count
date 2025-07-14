<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;

class LocationCrud extends Component
{
    public $locations, $name, $address, $locationId;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|unique:locations,name',
        'address' => 'nullable|string'
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
        $this->locationId = null;
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        Location::create([
            'name' => $this->name,
            'address' => $this->address
        ]);

        $this->dispatch('toast:success', message: 'Data lokasi berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $lokasi = Location::findOrFail($id);
        $this->name = $lokasi->name;
        $this->address = $lokasi->address;
        $this->locationId = $lokasi->id;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:locations,name,' . $this->locationId,
            'address' => 'nullable|string'
        ]);

        Location::find($this->locationId)->update([
            'name' => $this->name,
            'address' => $this->address
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
