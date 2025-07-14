<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-3">
        <div class="form-group mb-2">
            <label>Nama Lokasi</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-2">
            <label>Alamat</label>
            <input type="text" wire:model="address" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Tambah' }}
        </button>

        @if ($isEdit)
            <button type="button" wire:click="resetForm" class="btn btn-secondary">Batal</button>
        @endif
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($locations as $lokasi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lokasi->name }}</td>
                    <td>{{ $lokasi->address }}</td>
                    <td>
                        <button wire:click="edit({{ $lokasi->id }})" class="btn btn-sm btn-info">Edit</button>
                        {{-- <button onclick="confirmDelete({{ $lokasi->id }})" class="btn btn-sm btn-danger">
                            Hapus
                        </button> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- <script>
    function confirmDelete(id) {
        if (confirm('Yakin menghapus data ini?')) {
            @this.call('destroy', id)
        }
    }
</script> --}}

