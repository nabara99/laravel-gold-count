<div>
    <h3 class="h5 fw-bold mb-2">{{ $isEdit ? 'Edit Cashbon' : 'Tambah Cashbon' }}</h3>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
        <div class="row mb-2">
            <div class="col-md-3">
                <label class="form-label">Pekerja</label>
                <select wire:model="worker_id" class="form-select">
                    <option value="">Pilih</option>
                    @foreach ($workers as $worker)
                        <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                    @endforeach
                </select>
                @error('worker_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Lokasi</label>
                <select wire:model="location_id" class="form-select">
                    <option value="">Pilih</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('location_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" wire:model="date" class="form-control">
                @error('date')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Jumlah</label>
                <input type="number" wire:model="amount" class="form-control">
                @error('amount')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Keterangan</label>
                <input type="text" wire:model="description" class="form-control">
            </div>
        </div>
        <button class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Simpan' }}
        </button>
        @if ($isEdit)
            <button type="button" wire:click="resetForm" class="btn btn-secondary">Batal</button>
        @endif
    </form>
    <hr class="my-2">
    <div class="row mb-2">
        <div class="col-md-4">
            <label>Filter Lokasi</label>
            <select wire:model="filterLocation" class="form-control">
                <option value="">-- Semua Lokasi --</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Filter Pekerja</label>
            <select wire:model="filterWorker" class="form-control">
                <option value="">-- Semua Pekerja --</option>
                @foreach ($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button wire:click="applyFilter" class="btn btn-secondary">Terapkan Filter</button>
        </div>
    </div>


    <h3 class="h5 fw-bold mb-2">Daftar Cashbon</h3>

    <div class="table-responsive">
        <div class="col-md-3">
            <strong>Total Kasbon:</strong> Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Pekerja</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cashbons as $cb)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $cb->worker->name }}</td>
                        <td>{{ $cb->location->name }}</td>
                        <td>{{ $cb->date }}</td>
                        <td>Rp {{ number_format($cb->amount, 0, ',', '.') }}</td>
                        <td>
                            @if ($cb->status == 'unpaid')
                                <span class="badge bg-warning text-dark">Belum bayar</span>
                            @else
                                <span class="badge bg-success">Sudah bayar</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="edit({{ $cb->id }})" class="btn btn-sm btn-outline-info"
                                title="edit"><i class="bi bi-pencil-square"></i></button>
                            <button wire:click="delete({{ $cb->id }})" class="btn btn-sm btn-outline-danger"
                                title="hapus" onclick="confirm('Hapus data?') || event.stopImmediatePropagation()"><i
                                    class="bi bi-trash3-fill"></i></button>
                            @if ($cb->status == 'unpaid')
                                <button wire:click="markAsPaid({{ $cb->id }})"
                                    class="btn btn-sm btn-outline-success" title="Tandai Lunas">
                                    <i class="bi bi-cash-coin"></i>
                                </button>
                            @elseif ($cb->status == 'paid')
                                <button wire:click="markAsUnpaid({{ $cb->id }})"
                                    class="btn btn-sm btn-outline-warning" title="Batalkan Lunas">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Data tidak tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $cashbons->links() }}
    </div>
</div>
