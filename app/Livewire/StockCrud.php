<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Stock;
use App\Models\Location;
use Livewire\WithPagination;

class StockCrud extends Component
{
    use WithPagination;

    public $date, $weight, $location_id, $notes;
    public $isEdit = false;
    public $stockId;

    public $search = '';
    public $searchTerm = '';
    public $filterLocation = '';
    public $selectedLocation = '';

    public $totalWeight = 0;

    public $locations;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'date' => 'required|date',
        'weight' => 'required|numeric|min:0',
        'location_id' => 'required|exists:locations,id',
        'notes' => 'nullable|string',
    ];

    protected $updatesQueryString = ['search', 'filterLocation'];

    public function mount()
    {
        $this->locations = Location::all();
        $this->date = now()->toDateString();
        $this->selectedLocation = $this->filterLocation;
        $this->updateTotalWeight();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLocation()
    {
        $this->resetPage();
    }

    public function applyLocationFilter()
    {
        $this->filterLocation = $this->selectedLocation;
        $this->resetPage();
        $this->updateTotalWeight();
    }

    public function searchNow()
    {
        $this->searchTerm = $this->search;
        $this->resetPage();
        $this->updateTotalWeight();
    }

    public function store()
    {
        $this->validate();

        Stock::create([
            'date' => $this->date,
            'weight' => $this->weight,
            'location_id' => $this->location_id,
            'notes' => $this->notes,
        ]);

        $this->dispatch('toast:success', message: 'Stok berhasil ditambahkan.');
        $this->resetForm();
        $this->updateTotalWeight();
    }

    public function edit($id)
    {
        $this->isEdit = true;
        $stock = Stock::findOrFail($id);
        $this->stockId = $stock->id;
        $this->date = $stock->date;
        $this->weight = $stock->weight;
        $this->location_id = $stock->location_id;
        $this->notes = $stock->notes;
    }

    public function update()
    {
        $stock = Stock::findOrFail($this->stockId);

        $this->validate();

        $stock->update([
            'date' => $this->date,
            'weight' => $this->weight,
            'location_id' => $this->location_id,
            'notes' => $this->notes,
        ]);

        $this->dispatch('toast:success', message: 'Stok berhasil diperbarui.');
        $this->resetForm();
        $this->updateTotalWeight();
    }

    public function destroy($id)
    {
        Stock::destroy($id);
        $this->dispatch('toast:success', message: 'Stok berhasil dihapus.');
        $this->updateTotalWeight();
    }

    public function resetForm()
    {
        $this->reset(['date', 'weight', 'location_id', 'notes', 'isEdit', 'stockId']);
        $this->date = now()->toDateString();
    }

    public function updateTotalWeight()
    {
        $query = Stock::query();

        if ($this->searchTerm !== '') {
            $query->where('notes', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->filterLocation !== '') {
            $query->where('location_id', $this->filterLocation);
        }

        $this->totalWeight = $query->sum('weight');
    }

    public function render()
    {
        $query = Stock::with('location')
            ->when($this->searchTerm !== '', function ($q) {
                $q->where('notes', 'like', "%{$this->searchTerm}%");
            })
            ->when($this->filterLocation !== '', function ($q) {
                $q->where('location_id', $this->filterLocation);
            })
            ->orderByDesc('date');

        return view('livewire.stock-crud', [
            'stocks' => $query->paginate(10),
            'locations' => $this->locations,
        ]);
    }
}
