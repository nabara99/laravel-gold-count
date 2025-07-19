<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Investor</label>
                <select wire:model="user_id" class="form-control">
                    <option value="">-- Pilih Investor --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-3">
                <label>Lokasi</label>
                <select wire:model="location_id" class="form-control">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-2">
                <label>Jumlah Investasi</label>
                <input type="number" wire:model.defer="amount_invested" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Update' : 'Tambah' }}</button>
                @if ($isEdit)
                    <button class="btn btn-secondary" type="button" wire:click="resetForm">Batal</button>
                @endif
            </div>
        </div>
    </form>

    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Cari nama investor...">
        </div>
        <div class="col-md-4">
            <select wire:model.defer="selectedLocation" class="form-control">
                <option value="">-- Pilih Lokasi --</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" wire:click="applyLocationFilter">Filter</button>
        </div>
    </div>


    <div class="mb-2">
        <strong>Total Investasi:</strong> Rp {{ number_format($this->totalInvestasi, 2, ',', '.') }}
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered" style="font-size: 0.875rem">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Investor</th>
                    <th>Lokasi</th>
                    <th>Jumlah</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($investments as $inv)
                    <tr>
                        <td>{{ $loop->iteration + ($investments->currentPage() - 1) * $investments->perPage() }}</td>
                        <td>{{ $inv->user->name ?? '-' }}</td>
                        <td>{{ $inv->location->name ?? '-' }}</td>
                        <td>Rp {{ number_format($inv->amount_invested, 2, ',', '.') }}</td>
                        <td>{{ $inv->created_at->format('d-m-Y') }}</td>
                        <td>
                            <button wire:click="edit({{ $inv->id }})" class="btn btn-sm btn-info" title="edit"><i class="bi bi-pencil-square"></i></button>
                            {{-- <button onclick="confirmDelete({{ $inv->id }})" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')" title="hapus"><i class="bi bi-trash3-fill"></i></button> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        {{ $investments->links() }}
    </div>
</div>

{{-- <script>
    function confirmDelete(id) {
        if (confirm('Yakin menghapus data ini?')) {
            @this.call('destroy', id)
        }
    }
</script> --}}
