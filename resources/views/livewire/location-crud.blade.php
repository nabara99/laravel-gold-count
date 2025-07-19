<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label>Nama Lokasi</label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label>Alamat</label>
                    <input type="text" wire:model="address" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label>Persentase Investor (%)</label>
                    <input type="number" wire:model="percent_investor"
                        class="form-control @error('percent_investor') is-invalid @enderror" min="0"
                        max="100">
                    @error('percent_investor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label>Persentase Pekerja (%)</label>
                    <input type="number" wire:model="percent_worker"
                        class="form-control @error('percent_worker') is-invalid @enderror" min="0"
                        max="100">
                    @error('percent_worker')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
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
                <th>Investor (%)</th>
                <th>Pekerja (%)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($locations as $lokasi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lokasi->name }}</td>
                    <td>{{ $lokasi->address }}</td>
                    <td>{{ $lokasi->percent_investor }}%</td>
                    <td>{{ $lokasi->percent_worker }}%</td>
                    <td>
                        <button wire:click="edit({{ $lokasi->id }})" class="btn btn-sm btn-info">Edit</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
