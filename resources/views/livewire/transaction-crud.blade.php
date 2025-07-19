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

    <div class="row mb-2">
        <div class="col-md-4">
            <select wire:model="filterLocation" wire:change="$refresh" class="form-control">
                <option value="">-- Filter Lokasi --</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select wire:model="filterPeriod" class="form-control" @if (!$filterLocation) disabled @endif>
                <option value="">-- Filter Periode --</option>
                @forelse ($filterPeriods as $p)
                    <option value="{{ $p->id }}">{{ $p->date_start }} - {{ $p->date_end }}</option>
                @empty
                    <option value="">Tidak ada periode</option>
                @endforelse
            </select>

        </div>
        <div class="col-md-4">
            <button class="btn btn-secondary" wire:click="$refresh">Terapkan Filter</button>
        </div>
    </div>


    <div class="mb-2">
        <strong>Total Masuk:</strong> Rp {{ number_format($totalKredit, 2, ',', '.') }} |
        <strong>Total Keluar:</strong> Rp {{ number_format($totalDebit, 2, ',', '.') }} |
        <strong>Sisa:</strong> Rp {{ number_format($net, 2, ',', '.') }} |
        <strong>Pekerja ({{ $percentWorker }}%):</strong> Rp {{ number_format($toWorkers, 2, ',', '.') }} |
        <strong>Investor ({{ $percentInvestor }}%):</strong> Rp {{ number_format($toInvestors, 2, ',', '.') }}
    </div>


    <div class="table-responsive">
        <table class="table table-sm table-bordered" style="font-size: 0.875rem">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Periode</th>
                    <th>Uraian</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Tipe</th>
                    <th>mengurangi saldo?</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                        <td>{{ $trx->date }}</td>
                        <td>{{ $trx->location->name ?? '-' }}</td>
                        <td>{{ $trx->period->date_start ?? '-' }} s/d {{ $trx->period->date_end ?? '-' }}</td>
                        <td>{{ $trx->note ?? '-' }}</td>
                        <td>{{ $trx->qty }}</td>
                        <td>{{ number_format($trx->price) }}</td>
                        <td>{{ $trx->type === 'kredit' ? 'masuk' : ($trx->type === 'debit' ? 'keluar' : ucfirst($trx->type)) }}
                        </td>
                        <td>
                            @if ($trx->increase && $trx->type === 'debit')
                                Ya
                            @elseif (!$trx->increase && $trx->type === 'kredit')
                                -
                            @else
                                Tidak
                            @endif
                        </td>
                        <td>Rp {{ number_format($trx->amount, 2, ',', '.') }}</td>
                        <td>
                            <button wire:click="edit({{ $trx->id }})" class="btn btn-sm btn-info"
                                title="edit"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="confirmDelete({{ $trx->id }})" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus?')" title="hapus"><i
                                    class="bi bi-trash3-fill"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data transaksi</td>
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
