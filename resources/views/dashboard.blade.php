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

    </div>
@endsection
