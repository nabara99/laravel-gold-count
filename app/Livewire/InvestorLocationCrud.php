<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Location;
use App\Models\InvestorLocation;
use Livewire\WithPagination;

class InvestorLocationCrud extends Component
{
    use WithPagination;

    public $user_id, $location_id, $amount_invested;
    public $search = '';
    public $filterLocation = '';
    public $selectedLocation = '';
    public $locations = [];
    public $investorLocationId;
    public $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'location_id' => 'required|exists:locations,id',
        'amount_invested' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->locations = Location::orderBy('name')->get();
        $this->selectedLocation = $this->filterLocation;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterLocation() { $this->resetPage(); }

    public function applyLocationFilter()
    {
        $this->filterLocation = $this->selectedLocation;
        $this->resetPage();
    }

    public function store()
    {
        $this->validate();

        $user = User::find($this->user_id);
        if (!$user || $user->role->role_name !== 'investor') {
            $this->addError('user_id', 'Hanya user dengan role investor yang dapat dipilih.');
            return;
        }

        $exists = InvestorLocation::where('user_id', $this->user_id)
            ->where('location_id', $this->location_id)
            ->exists();

        if ($exists) {
            $this->dispatch('toast:error', message: 'Investor ini sudah memiliki data di lokasi tersebut');
            return;
        }

        InvestorLocation::create([
            'user_id' => $this->user_id,
            'location_id' => $this->location_id,
            'amount_invested' => $this->amount_invested ?? 0,
        ]);

        $this->resetForm();
        $this->dispatch('toast:success', message: 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->isEdit = true;
        $inv = InvestorLocation::findOrFail($id);
        $this->investorLocationId = $id;
        $this->user_id = $inv->user_id;
        $this->location_id = $inv->location_id;
        $this->amount_invested = $inv->amount_invested;
    }

    public function update()
    {
        $this->validate();

        $exists = InvestorLocation::where('user_id', $this->user_id)
            ->where('location_id', $this->location_id)
            ->where('id', '!=', $this->investorLocationId)
            ->exists();

        if ($exists) {
            $this->dispatch('toast:error', message: 'Investor ini sudah memiliki data di lokasi tersebut');
            return;
        }

        InvestorLocation::where('id', $this->investorLocationId)->update([
            'user_id' => $this->user_id,
            'location_id' => $this->location_id,
            'amount_invested' => $this->amount_invested ?? 0,
        ]);

        $this->resetForm();
        $this->dispatch('toast:success', message: 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        InvestorLocation::destroy($id);
        $this->dispatch('toast:success', message: 'Data berhasil dihapus');
    }

    public function resetForm()
    {
        $this->reset(['user_id', 'location_id', 'amount_invested', 'isEdit', 'investorLocationId']);
    }

    public function getTotalInvestasiProperty()
    {
        return InvestorLocation::when($this->filterLocation, fn($q) => $q->where('location_id', $this->filterLocation))
            ->sum('amount_invested');
    }

    public function render()
    {
        $query = InvestorLocation::with(['user', 'location'])
            ->when($this->search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->filterLocation, fn($q) => $q->where('location_id', $this->filterLocation));

        $users = User::whereHas('role', fn($q) =>
            $q->where('role_name', 'investor')
        )->orderBy('name')->get();

        return view('livewire.investor-location-crud', [
            'investments' => $query->paginate(10),
            'users' => $users,
        ]);
    }

}
