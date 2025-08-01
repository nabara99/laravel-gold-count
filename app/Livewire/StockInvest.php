<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stock;
use App\Models\Location;

class StockInvest extends Component
{
    use WithPagination;

    public $date, $weight, $location_id, $notes;
    public $stockId;

    public $search = '';
    public $searchTerm = '';
    public $filterLocation = '';
    public $selectedLocation = '';

    public $totalWeight = 0;

    public $locations;

    protected $paginationTheme = 'bootstrap';

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

        return view('livewire.stock-invest', [
            'stocks' => $query->paginate(10),
            'locations' => $this->locations,
        ]);
    }
}
