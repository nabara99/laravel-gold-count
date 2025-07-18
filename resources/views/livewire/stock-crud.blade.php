<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-1">
        <div class="row">
            <div class="col-md-3">
                <label>Tanggal</label>
                <input type="date" wire:model.defer="date" class="form-control @error('date') is-invalid @enderror">
                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-2">
                <label>Berat (gram)</label>
                <input type="number" wire:model.defer="weight" class="form-control @error('weight') is-invalid @enderror" step="0.01">
                @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label>Lokasi</label>
                <select wire:model.defer="location_id" class="form-control @error('location_id') is-invalid @enderror">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-2">
                <label>Catatan</label>
                <input type="text" wire:model.defer="notes" class="form-control">
            </div>
            <div class="col-md-2 py-4">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Tambah' }}</button>
                @if ($isEdit)
                    <button type="button" wire:click="resetForm" class="btn btn-secondary">Batal</button>
                @endif
            </div>
        </div>
    </form>

    <div class="row mb-2">
        <div class="col-md-4">
            <input type="text" wire:model.defer="search" wire:keydown.enter="searchNow" class="form-control" placeholder="Cari catatan, tekan Enter">
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-8">
                    <select wire:model.defer="selectedLocation" class="form-control">
                        <option value="">-- Pilih Lokasi, lalu klik Filter --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <button class="btn btn-secondary" wire:click="applyLocationFilter">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <strong>Total Berat:</strong> {{ number_format($totalWeight, 2) }} gram
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Berat (gram)</th>
                    <th>Lokasi</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($stocks->currentPage() - 1) * $stocks->perPage() }}</td>
                        <td>{{ $item->date }}</td>
                        <td>{{ number_format($item->weight, 2) }}</td>
                        <td>{{ $item->location->name ?? '-' }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                        <td>
                            <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-info" title="edit"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')" title="hapus"><i class="bi bi-trash3-fill"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $stocks->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Yakin menghapus data ini?')) {
            @this.call('destroy', id)
        }
    }
</script>
