<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Tanggal Mulai</label>
                <input type="date" wire:model.defer="date_start" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Tanggal Selesai</label>
                <input type="date" wire:model.defer="date_end" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Lokasi</label>
                <select wire:model.defer="location_id" class="form-control">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary" type="submit">
                    {{ $isEdit ? 'Update' : 'Tambah' }}
                </button>
                @if ($isEdit)
                    <button class="btn btn-secondary" type="button" wire:click="resetForm">Batal</button>
                @endif
            </div>
        </div>
    </form>

    <div class="row mb-3">
    <div class="col-md-4">
        <select wire:model.defer="filterLocationInput" class="form-control">
            <option value="">-- Filter Lokasi --</option>
            @foreach ($locations as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button wire:click="applyLocationFilter" class="btn btn-secondary w-100">Filter</button>
    </div>
</div>


    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Periode</th>
                    <th>Lokasi</th>
                    <th>Total</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periods as $period)
                    <tr>
                        <td>{{ $loop->iteration + ($periods->currentPage() - 1) * $periods->perPage() }}</td>
                        <td>{{ $period->date_start }} s.d {{ $period->date_end }}</td>
                        <td>{{ $period->location->name ?? '-' }}</td>
                        <td>Rp {{ number_format($period->total_money, 2, ',', '.') }}</td>
                        <td>{{ $period->created_at->format('d/m/Y') }}</td>
                        <td>
                            <button wire:click="edit({{ $period->id }})" class="btn btn-sm btn-info">Edit</button>
                            <button wire:click="destroy({{ $period->id }})" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $periods->links() }}
    </div>
</div>
