<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Worker;
use App\Models\Location;
use Livewire\WithPagination;

class WorkerCrud extends Component
{
    use WithPagination;

    public $name, $location_id, $age, $phone_number;
    public $isEdit = false;
    public $workerId;
    public $locations;

    public $search = '';
    public $searchTerm = '';
    public $filterLocation = '';
    public $selectedLocation = '';


    protected $paginationTheme = 'bootstrap';

    protected $updatesQueryString = ['search', 'filterLocation'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLocation()
    {
        $this->resetPage();
    }

    protected $rules = [
        'name' => 'required|string|unique:workers,name',
        'location_id' => 'required|exists:locations,id',
        'age' => 'nullable|integer',
        'phone_number' => 'nullable|string',
    ];

    public function mount()
    {
        $this->locations = Location::all();
        $this->selectedLocation = $this->filterLocation;
    }

    public function applyLocationFilter()
    {
        $this->filterLocation = $this->selectedLocation;
        $this->resetPage();
    }

    public function render()
    {
        $query = Worker::with('location')
            // Jalankan pencarian jika searchTerm tidak kosong
            ->when($this->searchTerm !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->searchTerm}%")
                        ->orWhere('phone_number', 'like', "%{$this->searchTerm}%");
                });
            })
            // Jalankan filter lokasi kapan pun nilainya tidak null
            ->when($this->filterLocation !== '', function ($q) {
                $q->where('location_id', $this->filterLocation);
            })
            ->orderBy('location_id');

        return view('livewire.worker-crud', [
            'workers' => $query->paginate(8),
            'locations' => $this->locations
        ]);
    }

    public function searchNow()
    {
        $this->searchTerm = $this->search;
        $this->resetPage();
    }

    public function store()
    {
        $this->validate();

        Worker::create([
            'name' => $this->name,
            'location_id' => $this->location_id,
            'age' => $this->age,
            'phone_number' => $this->phone_number,
        ]);

        $this->dispatch('toast:success', message: 'Pekerja lokasi berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->isEdit = true;
        $worker = Worker::findOrFail($id);
        $this->workerId = $worker->id;
        $this->name = $worker->name;
        $this->location_id = $worker->location_id;
        $this->age = $worker->age;
        $this->phone_number = $worker->phone_number;
    }

    public function update()
    {
        $worker = Worker::findOrFail($this->workerId);

        $this->validate([
            'name' => 'required|string|unique:workers,name,' . $worker->id,
            'location_id' => 'required|exists:locations,id',
            'age' => 'nullable|integer',
            'phone_number' => 'nullable|string',
        ]);

        $worker->update([
            'name' => $this->name,
            'location_id' => $this->location_id,
            'age' => $this->age,
            'phone_number' => $this->phone_number,
        ]);

        $this->dispatch('toast:success', message: 'Pekerja lokasi berhasil diupdate.');
        $this->resetForm();
    }

    public function destroy($id)
    {
        Worker::destroy($id);
        $this->dispatch('toast:success', message: 'Pekerja lokasi berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'location_id', 'age', 'phone_number', 'isEdit', 'workerId']);
    }
}
