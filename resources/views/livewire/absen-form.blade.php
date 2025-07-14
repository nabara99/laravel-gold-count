<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="location_id">Pilih Lokasi</label>
            <select wire:model="location_id" class="form-control">
                <option value="">-- Pilih Lokasi --</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="date">Tanggal</label>
            <input type="date" wire:model="date" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button wire:click="loadWorkers" class="btn btn-info w-100">
                <i class="bi bi-people-fill me-1"></i> Muat Pekerja
            </button>
        </div>
    </div>

    @if (!empty($absensi))
        <form wire:submit.prevent="save">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pekerja</th>
                        <th>
                            Hadir
                            <input type="checkbox" wire:model.lazy="selectAll" class="ms-2">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workers as $index => $worker)
                        <tr>
                            <td style="width: 15px">{{ ++$index }}</td>
                            <td>{{ $worker->name }}</td>
                            <td>
                                <input type="checkbox" wire:model="absensi.{{ $worker->id }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-success">Simpan Absensi</button>
        </form>
    @endif

    @if ($this->absenSummary)
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                Rekap Absensi Tanggal {{ $this->absenSummary['tanggal'] }} - Lokasi: {{ $this->absenSummary['lokasi'] }}
            </div>
            <div class="card-body p-2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Pekerja Hadir</th>
                            <th>Pekerja Tidak Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                @if (count($this->absenSummary['hadir']))
                                    {{ implode(', ', $this->absenSummary['hadir']) }}
                                @else
                                    <em>Tidak ada data</em>
                                @endif
                            </td>
                            <td>
                                @if (count($this->absenSummary['tidak_hadir']))
                                    {{ implode(', ', $this->absenSummary['tidak_hadir']) }}
                                @else
                                    <em>Tidak ada data</em>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif


</div>
