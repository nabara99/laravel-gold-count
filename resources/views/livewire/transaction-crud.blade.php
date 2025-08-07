<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-3">
        <div class="row mb-3">
            <div class="col-md-2">
                <label>Tanggal</label>
                <input type="date" wire:model.defer="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Lokasi</label>
                <select wire:model="location_id" wire:change="$refresh" class="form-control">
                    <option value="">-- Lokasi --</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('location_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-3">
                <label>Periode</label>
                <select wire:model.defer="period_id" class="form-control" wire:key="form-period-{{ $location_id }}">
                    <option value="">-- Periode --</option>
                    @forelse ($formPeriods as $p)
                        <option value="{{ $p->id }}">{{ $p->date_start }} s/d {{ $p->date_end }}</option>
                    @empty
                        <option value="">Tidak ada periode tersedia</option>
                    @endforelse
                </select>
                @error('period_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

            </div>
            <div class="col-md-2">
                <label>Qty</label>
                <input type="number" wire:model.defer="qty" class="form-control" step="0.01">
                @error('qty')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-2">
                <label>Harga</label>
                <input type="number" wire:model.defer="price" class="form-control">
                @error('price')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-2">
                <label>Tipe</label>
                <select wire:model.defer="type" class="form-control">
                    <option value="">--</option>
                    <option value="kredit">Masuk</option>
                    <option value="debit">Keluar</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label>Mengurangi Saldo?</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="increase" id="increaseCheck">
                    <label class="form-check-label" for="increaseCheck">
                        Ya, ini mengurangi saldo
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <label>Catatan</label>
                <input type="text" wire:model.defer="note" class="form-control" placeholder="Catatan (opsional)">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-1">{{ $isEdit ? 'Update' : 'Tambah' }}</button>
        @if ($isEdit)
            <button type="button" wire:click="resetForm" class="btn btn-secondary">Batal</button>
        @endif
    </form>

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0">Filter Data</h6>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="form-label">Filter Lokasi</label>
                    <select wire:model="filterLocation" wire:change="$refresh" class="form-control">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Periode</label>
                    <select wire:model="filterPeriod" class="form-control" @if (!$filterLocation) disabled @endif>
                        <option value="">-- Semua Periode --</option>
                        @forelse ($filterPeriods as $p)
                            <option value="{{ $p->id }}">{{ $p->date_start }} - {{ $p->date_end }}</option>
                        @empty
                            <option value="">Tidak ada periode</option>
                        @endforelse
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
                        <button class="btn btn-primary" wire:click="applyFilters">
                            <i class="bi bi-funnel"></i> Terapkan
                        </button>
                        <button class="btn btn-outline-secondary" wire:click="clearFilters">
                            <i class="bi bi-x-circle"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filter Indicators -->
            @if($filterLocation || $filterPeriod || $filterMonth)
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">
                            Filter aktif:
                            @if($filterLocation)
                                <span class="badge bg-info me-1">Lokasi: {{ $locations->find($filterLocation)->name ?? 'Unknown' }}</span>
                            @endif
                            @if($filterPeriod)
                                @php
                                    $activePeriod = $filterPeriods->find($filterPeriod);
                                @endphp
                                <span class="badge bg-info me-1">Periode: {{ $activePeriod ? $activePeriod->date_start . ' - ' . $activePeriod->date_end : 'Unknown' }}</span>
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
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="text-success">
                            <strong>Total Masuk:</strong> Rp {{ number_format($totalKredit, 2, ',', '.') }}
                        </div>
                        <div class="text-success">
                            <strong>Total Qty Masuk:</strong> {{ number_format($totalQtyKredit, 2, ',', '.') }}
                        </div>
                        <div class="text-danger">
                            <strong>Total Keluar:</strong> Rp {{ number_format($totalDebit, 2, ',', '.') }}
                        </div>
                        <div class="text-primary">
                            <strong>Sisa:</strong> Rp {{ number_format($net, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <div class="text-info">
                            <strong>Pekerja ({{ $percentWorker }}%):</strong> Rp {{ number_format($toWorkers, 2, ',', '.') }}
                        </div>
                        <div class="text-warning">
                            <strong>Investor ({{ $percentInvestor }}%):</strong> Rp {{ number_format($toInvestors, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" wire:model.debounce.500ms="searchNote" placeholder="Cari catatan...">
        </div>
        <div class="col-md-3">
            <select class="form-control" wire:model="searchType">
                <option value="">-- Semua Tipe --</option>
                <option value="kredit">Masuk</option>
                <option value="debit">Keluar</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-control" wire:model="searchIncrease">
                <option value="">-- Semua --</option>
                <option value="1">Ya (Mengurangi Saldo)</option>
                <option value="0">Tidak</option>
            </select>
        </div>
        <div class="col-md-2">
            <button wire:click="$refresh" class="btn btn-secondary">Cari</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered" style="font-size: 0.875rem">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Periode</th>
                    <th>Uraian</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Tipe</th>
                    <th>Mengurangi Saldo?</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                        <td>{{ $trx->location->name ?? '-' }}</td>
                        <td>
                            @if($trx->period)
                                {{ \Carbon\Carbon::parse($trx->period->date_start)->format('d/m/Y') }} s/d
                                {{ \Carbon\Carbon::parse($trx->period->date_end)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $trx->note ?? '-' }}</td>
                        <td>{{ number_format($trx->qty, 2, ',', '.') }}</td>
                        <td>Rp {{ number_format($trx->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $trx->type === 'kredit' ? 'bg-success' : 'bg-danger' }}">
                                {{ $trx->type === 'kredit' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td>
                            @if ($trx->increase && $trx->type === 'debit')
                                <span class="badge bg-warning">Ya</span>
                            @elseif (!$trx->increase && $trx->type === 'kredit')
                                <span class="badge bg-secondary">-</span>
                            @else
                                <span class="badge bg-light text-dark">Tidak</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold {{ $trx->type === 'kredit' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($trx->amount, 2, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button wire:click="edit({{ $trx->id }})" class="btn btn-sm btn-info" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button onclick="confirmDelete({{ $trx->id }})" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $transactions->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Yakin menghapus data ini?')) {
            @this.call('destroy', id)
        }
    }
</script>
