@extends('layouts.app')

@section('main')
<main class="app-main">
    <div class="app-content mt-1">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">Laporan Penghasilan Pekerja</div>
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
                            <button id="showIncomeBtn" class="btn btn-primary w-100" disabled>Tampilkan Laporan</button>
                        </div>
                    </div>

                    <div id="incomeResult"></div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(function () {
    $('#lokasiSelect').change(function () {
        const lokasiId = $(this).val();
        $('#periodeSelect').empty().append('<option value="">-- Pilih Periode --</option>').prop('disabled', true);
        $('#showIncomeBtn').prop('disabled', true);
        $('#incomeResult').html('');

        if (lokasiId) {
            $.get(`/laporan-penghasilan/periods/${lokasiId}`, function (data) {
                data.forEach(p => {
                    $('#periodeSelect').append(`<option value="${p.id}">${p.date_start} s/d ${p.date_end}</option>`);
                });
                $('#periodeSelect').prop('disabled', false);
            });
        }
    });

    $('#periodeSelect').change(function () {
        $('#showIncomeBtn').prop('disabled', !$(this).val());
    });

    $('#showIncomeBtn').click(function () {
        const lokasiId = $('#lokasiSelect').val();
        const periodeId = $('#periodeSelect').val();

        if (!lokasiId || !periodeId) return;

        $.get('/laporan-penghasilan/data', {
            location_id: lokasiId,
            period_id: periodeId
        }, function (data) {
            if (data.length === 0) {
                $('#incomeResult').html('<div class="alert alert-info">Tidak ada data ditemukan.</div>');
                return;
            }

            let html = `<div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr>
                        <th>Nama Pekerja</th>
                        <th>Jumlah Hadir</th>
                        <th>Penghasilan (Rp)</th>
                    </tr></thead>
                    <tbody>`;

            data.forEach(item => {
                html += `<tr>
                    <td>${item.name}</td>
                    <td>${item.jumlah_hadir}</td>
                    <td>Rp ${Number(item.penghasilan).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            $('#incomeResult').html(html);
        });
    });
});
</script>
@endpush
