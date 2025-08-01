<div>
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
