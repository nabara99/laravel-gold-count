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

    <hr class="my-3">

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="form-label">Filter Lokasi</label>
                    <select wire:model="filterLocation" class="form-control">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Pekerja</label>
                    <select wire:model="filterWorker" class="form-control">
                        <option value="">-- Semua Pekerja --</option>
                        @foreach ($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Bulan</label>
                    <select wire:model.defer="selectedMonth" class="form-control">
                        <option value="">-- Semua Bulan --</option>
                        @foreach ($monthOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button wire:click="applyFilter" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Terapkan
                        </button>
                        <button wire:click="clearFilters" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filter Indicators -->
            @if($filterLocation || $filterWorker || $filterMonth)
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">
                            Filter aktif:
                            @if($filterLocation)
                                <span class="badge bg-info me-1">Lokasi: {{ $locations->find($filterLocation)->name ?? 'Unknown' }}</span>
                            @endif
                            @if($filterWorker)
                                <span class="badge bg-info me-1">Pekerja: {{ $workers->find($filterWorker)->name ?? 'Unknown' }}</span>
                            @endif
                            @if($filterMonth)
                                <span class="badge bg-info me-1">Bulan: {{ $monthOptions[$filterMonth] ?? $filterMonth }}</span>
                            @endif
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Section -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-0">
                        <i class="bi bi-cash-coin"></i> Total Kasbon:
                        <span class="fw-bold">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Total berdasarkan filter yang aktif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <h3 class="h5 fw-bold mb-2">Daftar Cashbon</h3>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Pekerja</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cashbons as $cb)
                    <tr>
                        <td>{{ $loop->iteration + ($cashbons->currentPage() - 1) * $cashbons->perPage() }}</td>
                        <td>{{ $cb->worker->name }}</td>
                        <td>{{ $cb->location->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($cb->date)->format('d/m/Y') }}</td>
                        <td>
                            <span class="fw-bold text-primary">
                                Rp {{ number_format($cb->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ $cb->description ?? '-' }}</td>
                        <td>
                            @if ($cb->status == 'unpaid')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock"></i> Belum Bayar
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Sudah Bayar
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button wire:click="edit({{ $cb->id }})" class="btn btn-sm btn-outline-info"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:click="delete({{ $cb->id }})" class="btn btn-sm btn-outline-danger"
                                    title="Hapus" onclick="confirm('Hapus data?') || event.stopImmediatePropagation()">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Data tidak tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $cashbons->links() }}
    </div>
</div>
