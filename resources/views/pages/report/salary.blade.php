@extends('layouts.app')

@section('main')
    <main class="app-main">
        <div class="app-content mt-1">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Laporan Penghasilan Pekerja</h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Lokasi</label>
                                <select id="lokasiSelect" class="form-control">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Periode</label>
                                <select id="periodeSelect" class="form-control" disabled>
                                    <option value="">-- Pilih Periode --</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="showIncomeBtn" class="btn btn-primary w-100" disabled>
                                    <i class="bi bi-bar-chart"></i> Tampilkan Laporan
                                </button>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div id="summarySection" style="display: none;" class="mb-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Ringkasan Laporan</h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-success fw-bold" id="totalKredit">Rp 0</div>
                                                        <small class="text-muted">Total Pemasukan</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-danger fw-bold" id="totalDebit">Rp 0</div>
                                                        <small class="text-muted">Total Pengeluaran</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-primary fw-bold" id="totalProfit">Rp 0</div>
                                                        <small class="text-muted">Keuntungan</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-warning fw-bold" id="totalStockWeight">0 gram</div>
                                                        <small class="text-muted">Total Berat Stok</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="text-center">
                                                        <div class="text-info fw-bold" id="totalForWorkers">Rp 0</div>
                                                        <small class="text-muted">Total untuk Pekerja</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="text-center">
                                                        <div class="text-muted fw-bold" id="periodRange">-</div>
                                                        <small class="text-muted">Periode</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2
            }).format(amount);
        }

        function formatWeight(weight) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(weight) + ' gram';
        }

        $(function() {
            $('#lokasiSelect').change(function() {
                const lokasiId = $(this).val();
                $('#periodeSelect').empty().append('<option value="">-- Pilih Periode --</option>').prop(
                    'disabled', true);
                $('#showIncomeBtn').prop('disabled', true);
                $('#incomeResult').html('');
                $('#summarySection').hide();

                if (lokasiId) {
                    $.get(`/laporan-penghasilan/periods/${lokasiId}`, function(data) {
                        data.forEach(p => {
                            $('#periodeSelect').append(
                                `<option value="${p.id}">${p.date_start} s/d ${p.date_end}</option>`
                            );
                        });
                        $('#periodeSelect').prop('disabled', false);
                    });
                }
            });

            $('#periodeSelect').change(function() {
                $('#showIncomeBtn').prop('disabled', !$(this).val());
            });

            $('#showIncomeBtn').click(function() {
                const lokasiId = $('#lokasiSelect').val();
                const periodeId = $('#periodeSelect').val();

                if (!lokasiId || !periodeId) return;

                // Show loading
                $('#incomeResult').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#summarySection').hide();

                $.get('/laporan-penghasilan/data', {
                    location_id: lokasiId,
                    period_id: periodeId
                }, function(response) {
                    const data = response.workers;
                    const summary = response.summary;

                    if (data.length === 0) {
                        $('#incomeResult').html(
                            '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Tidak ada data pekerja ditemukan.</div>'
                        );
                        $('#summarySection').hide();
                        return;
                    }

                    // Update summary section
                    $('#totalKredit').text(formatCurrency(summary.total_kredit));
                    $('#totalDebit').text(formatCurrency(summary.total_debit));
                    $('#totalProfit').text(formatCurrency(summary.profit));
                    $('#totalStockWeight').text(formatWeight(summary.total_stock_weight));
                    $('#totalForWorkers').text(formatCurrency(summary.total_for_workers));
                    $('#periodRange').text(summary.period_range);
                    $('#summarySection').show();

                    // Build worker table
                    let html = `<div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Penghasilan Pekerja - ${summary.location_name}</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pekerja</th>
                                            <th>Jumlah Hadir</th>
                                            <th>Penghasilan</th>
                                            <th>Kasbon</th>
                                            <th>Yang Diterima</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                    let totalPenghasilan = 0;
                    let totalKasbon = 0;
                    let totalTerima = 0;

                    data.forEach((item, index) => {
                        const penghasilan = parseFloat(item.penghasilan);
                        const kasbon = parseFloat(item.cashbon || 0);
                        const terima = parseFloat(item.terima);

                        totalPenghasilan += penghasilan;
                        totalKasbon += kasbon;
                        totalTerima += terima;

                        html += `<tr>
                            <td>${index + 1}</td>
                            <td class="fw-bold">${item.name}</td>
                            <td class="text-center">
                                <span class="badge bg-info">${item.jumlah_hadir} hari</span>
                            </td>
                            <td class="text-end text-success fw-bold">${formatCurrency(penghasilan)}</td>
                            <td class="text-end text-danger">${formatCurrency(kasbon)}</td>
                            <td class="text-end text-primary fw-bold">${formatCurrency(terima)}</td>
                        </tr>`;
                    });

                    // Add totals row
                    html += `<tr class="table-warning fw-bold">
                        <td colspan="3" class="text-end">TOTAL:</td>
                        <td class="text-end text-success">${formatCurrency(totalPenghasilan)}</td>
                        <td class="text-end text-danger">${formatCurrency(totalKasbon)}</td>
                        <td class="text-end text-primary">${formatCurrency(totalTerima)}</td>
                    </tr>`;

                    html += '</tbody></table></div></div></div>';
                    $('#incomeResult').html(html);
                }).fail(function() {
                    $('#incomeResult').html(
                        '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan saat memuat data.</div>'
                    );
                    $('#summarySection').hide();
                });
            });
        });
    </script>
@endpush
