@extends('layouts.app')

@section('title', 'Attendance | ')

@section('main')
    <main class="app-main">
        <div class="app-content mt-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Laporan Absensi Pekerja</h3>
                            </div>
                            <div class="card-body p-2">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Lokasi</label>
                                        <select id="lokasiSelect" class="form-control">
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach ($locations as $loc)
                                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Periode</label>
                                        <select id="periodeSelect" class="form-control" disabled>
                                            <option value="">-- Pilih Periode --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button id="filterBtn" class="btn btn-primary w-100" disabled>Tampilkan
                                            Laporan</button>
                                    </div>
                                </div>

                                <div id="laporanContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            $('#lokasiSelect').change(function() {
                const lokasiId = $(this).val();
                $('#periodeSelect').empty().append('<option value="">-- Pilih Periode --</option>');
                $('#periodeSelect').prop('disabled', true);
                $('#filterBtn').prop('disabled', true);

                if (lokasiId) {
                    $.get(`/laporan-absensi/periods/${lokasiId}`, function(data) {
                        if (data.length > 0) {
                            data.forEach(p => {
                                $('#periodeSelect').append(
                                    `<option value="${p.id}">${p.date_start} s/d ${p.date_end}</option>`
                                );
                            });
                            $('#periodeSelect').prop('disabled', false);
                        }
                    });
                }
            });

            $('#periodeSelect').change(function() {
                $('#filterBtn').prop('disabled', !$(this).val());
            });

            $('#filterBtn').click(function() {
                const lokasiId = $('#lokasiSelect').val();
                const periodeId = $('#periodeSelect').val();

                if (!lokasiId || !periodeId) return;

                $.get('/laporan-absensi/data', {
                    location_id: lokasiId,
                    period_id: periodeId
                }, function(data) {
                    if (data.length === 0) {
                        $('#laporanContainer').html(
                            `<div class="alert alert-info">Tidak ada data absensi ditemukan.</div>`
                        );
                        return;
                    }

                    let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Pekerja</th>
                                <th>Tanggal Masuk</th>
                                <th>Tanggal Tidak Masuk</th>
                                <th>Jumlah Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                    data.forEach(row => {
                        html += `
                    <tr>
                        <td>${row.name}</td>
                        <td>${row.hadir_dates.length > 0 ? '<ul>' + row.hadir_dates.map(t => `<li>${t}</li>`).join('') + '</ul>' : '-'}</td>
                        <td>${row.tidak_hadir_dates.length > 0 ? '<ul>' + row.tidak_hadir_dates.map(t => `<li>${t}</li>`).join('') + '</ul>' : '-'}</td>
                        <td><strong>${row.jumlah_hadir}</strong></td>
                    </tr>
                `;
                    });

                    html += '</tbody></table></div>';
                    $('#laporanContainer').html(html);
                });
            });
        });
    </script>
@endpush
