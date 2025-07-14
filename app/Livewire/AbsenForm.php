<?php

namespace App\Livewire;

use App\Models\Absen;
use App\Models\Worker;
use App\Models\Location;
use Livewire\Component;
use Illuminate\Support\Carbon;

class AbsenForm extends Component
{
    public $location_id;
    public $date;
    public $workers = [];
    public $absensi = [];

    public $selectAll = false;

    protected $rules = [
        'location_id' => 'required|exists:locations,id',
        'date' => 'required|date',
    ];

    public function updatedLocationId()
    {
        $this->reset(['workers', 'absensi']);
        if ($this->date) {
            $this->loadWorkers();
        }
    }


    public function updatedDate()
    {
        if ($this->location_id) {
            $this->loadWorkers();
        }
    }

    public function mount()
    {
        $this->date = now()->toDateString();
    }

    public function loadWorkers()
    {
        $this->workers = Worker::where('location_id', $this->location_id)
            ->orderBy('name')
            ->get();

        $this->absensi = [];

        foreach ($this->workers as $worker) {
            $existing = Absen::where('worker_id', $worker->id)
                ->where('date', $this->date)
                ->first();

            $this->absensi[$worker->id] = $existing && $existing->status === 'hadir';
        }

        $this->selectAll = collect($this->absensi)->every(fn($v) => $v);
    }

    public function updatedSelectAll($value)
    {
        foreach ($this->workers as $worker) {
            $this->absensi[$worker->id] = $value;
        }
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'absensi.')) {
            // Cek jika semua pekerja dicentang
            $this->selectAll = collect($this->workers)->every(function ($worker) {
                return isset($this->absensi[$worker->id]) && $this->absensi[$worker->id];
            });
        }

        if ($property === 'selectAll') {
            foreach ($this->workers as $worker) {
                $this->absensi[$worker->id] = $this->selectAll;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $validWorkerIds = $this->workers->pluck('id')->toArray();

        Absen::where('location_id', $this->location_id)
            ->where('date', $this->date)
            ->whereIn('worker_id', $validWorkerIds)
            ->delete();

        foreach ($validWorkerIds as $workerId) {
            $status = !empty($this->absensi[$workerId]) && $this->absensi[$workerId] ? 'hadir' : 'tidak hadir';

            Absen::updateOrCreate(
                ['worker_id' => $workerId, 'date' => $this->date],
                [
                    'location_id' => $this->location_id,
                    'status' => $status,
                ]
            );
        }

        $this->dispatch('toast:success', message: 'Absensi berhasil diperbarui.');
        $this->loadWorkers();
    }

    public function getAbsenSummaryProperty()
    {
        if (!$this->location_id || !$this->date) return null;

        $location = Location::find($this->location_id);

        $absens = Absen::with('worker')
            ->where('location_id', $this->location_id)
            ->where('date', $this->date)
            ->get();

        $hadir = $absens->where('status', 'hadir')->pluck('worker.name')->all();
        $tidakHadir = $absens->where('status', 'tidak hadir')->pluck('worker.name')->all();

        return [
            'lokasi' => $location?->name ?? '-',
            'tanggal' => $this->date,
            'hadir' => $hadir,
            'tidak_hadir' => $tidakHadir,
        ];
    }

    public function render()
    {
        return view('livewire.absen-form', [
            'locations' => Location::orderBy('name')->get(),
        ]);
    }
}
