<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-1">
        <div class="row">
            <div class="col-md-3">
                <label for="name">Nama</label>
                <input type="text" id="name" wire:model.defer="name" class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class=" col-md-3">
                <label for="location">Lokasi</label>
                <select wire:model.defer="location_id" class="form-control @error('location_id') is-invalid @enderror">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-2">
                <label for="age">Usia</label>
                <input type="number" wire:model.defer="age" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="phone">Nomor Telepon</label>
                <input type="number" wire:model.defer="phone_number" class="form-control">
            </div>
            <div class="col-md-2 py-4">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Tambah' }}</button>
                @if ($isEdit)
                    <button type="button" wire:click="resetForm" class="btn btn-secondary">Batal</button>
                @endif
            </div>
        </div>
    </form>

    <div class="row mb-2 mt-0">
        <div class="col-md-5">
            <input type="text" wire:model.defer="search" class="form-control"
                wire:keydown.enter="searchNow"
                placeholder="Ketik nama atau telepon, lalu tekan Enter">
        </div>

        <div class="col-md-7">
            <div class="row">
                <div class="col-8">
                    <select wire:model.defer="selectedLocation" class="form-control">
                        <option value="">-- Pilih Lokasi, lanjut klik filter --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <button class="btn btn-md btn-secondary" wire:click="applyLocationFilter">Filter</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Lokasi</th>
                    <th>Usia</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workers as $worker)
                    <tr>
                        <td>{{ $loop->iteration + ($workers->currentPage() - 1) * $workers->perPage() }}</td>
                        <td>{{ $worker->name }}</td>
                        <td>{{ $worker->location->name ?? '-' }}</td>
                        <td>{{ $worker->age ?? '-' }}</td>
                        <td>{{ $worker->phone_number ?? '-' }}</td>
                        <td>
                            <button wire:click="edit({{ $worker->id }})" class="btn btn-sm btn-info" title="edit"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="confirmDelete({{ $worker->id }})" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')" title="hapus"><i class="bi bi-trash3-fill"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $workers->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Yakin menghapus data ini?')) {
            @this.call('destroy', id)
        }
    }
</script>
