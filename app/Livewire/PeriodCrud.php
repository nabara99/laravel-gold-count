<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Period;
use App\Models\Location;
use Livewire\WithPagination;

class PeriodCrud extends Component
{
    use WithPagination;

    public $date_start, $date_end, $location_id;
    public $periodId, $isEdit = false;
    public $locations;
    public $filterLocationInput = '';
    public $filterLocation = '';

    protected $rules = [
        'date_start' => 'required|date',
        'date_end' => 'required|date|after_or_equal:date_start',
        'location_id' => 'required|exists:locations,id',
    ];

    public function mount()
    {
        $this->locations = Location::orderBy('name')->get();
    }

    public function applyLocationFilter()
    {
        $this->filterLocation = $this->filterLocationInput;
        $this->resetPage();
    }

    public function store()
    {
        $this->validate();
        Period::create([
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'location_id' => $this->location_id,
        ]);
        $this->resetForm();
        $this->dispatch('toast:success', message: 'Periode berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->isEdit = true;
        $period = Period::findOrFail($id);
        $this->periodId = $period->id;
        $this->date_start = $period->date_start;
        $this->date_end = $period->date_end;
        $this->location_id = $period->location_id;
    }

    public function update()
    {
        $this->validate();
        Period::where('id', $this->periodId)->update([
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'location_id' => $this->location_id,
        ]);
        $this->resetForm();
        $this->dispatch('toast:success', message: 'Periode berhasil diperbarui');
    }

    public function destroy($id)
    {
        Period::destroy($id);
        $this->dispatch('toast:success', message: 'Periode berhasil dihapus');
    }

    public function resetForm()
    {
        $this->reset(['date_start', 'date_end', 'location_id', 'periodId', 'isEdit']);
    }

    public function render()
    {
        $periods = Period::with('location')
        ->when($this->filterLocation, fn($q) => $q->where('location_id', $this->filterLocation))
        ->orderByDesc('date_start')
        ->paginate(10);

        return view('livewire.period-crud', [
            'periods' => $periods,
        ]);
    }
}
