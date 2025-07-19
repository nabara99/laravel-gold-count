@extends('layouts.app')

@section('main')
    <div class="container my-4">
        <h2 class="mb-4">Dashboard</h2>

        @php
            $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
        @endphp

        <div class="row">
            @foreach ($dashboardData as $index => $data)
                @php
                    $color = $colors[$index % count($colors)];
                @endphp
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-{{ $color }} text-white">
                            <h5 class="mb-0">{{ $data['location'] }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Total Emas
                                        Didapat:</strong><br>{{ $data['total_gram_didapat'] }} gram</li>
                                <li class="list-group-item"><strong>Saldo Emas Saat
                                        Ini:</strong><br>{{ $data['saldo_gram'] }} gram</li>
                                <li class="list-group-item"><strong>Total Pengeluaran:</strong><br>Rp
                                    {{ number_format($data['rekap_pengeluaran'], 0, ',', '.') }}</li>
                                <li class="list-group-item"><strong>Saldo Uang Saat Ini:</strong><br>Rp
                                    {{ number_format($data['saldo_uang'], 0, ',', '.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title">Emas Masuk per Tanggal</h3>
                    <a href="#">Bulan: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</a>
                </div>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <canvas id="emas-chart" height="200"></canvas>
                </div>
                <div class="d-flex flex-wrap">
                    @foreach ($dataGrafik as $lokasi)
                        <span class="mr-3 mb-1">
                            <i class="fas fa-square"
                                style="color: {{ ['blue', 'green', 'orange', 'purple', 'red', 'gray'][$loop->index % 6] }}"></i>
                            {{ $lokasi['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('scripts')
    <script>
        const labels = @json($tanggalList);
        const datasets = @json($dataGrafik).map((item, index) => {
            const colors = ['blue', 'green', 'orange', 'purple', 'red', 'gray'];
            return {
                label: item.label,
                data: item.data,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length],
                tension: 0.4,
                fill: false
            };
        });

        const ctx = document.getElementById('emas-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Emas (gram)'
                        }
                    }
                }
            }
        });
    </script>
@endpush
