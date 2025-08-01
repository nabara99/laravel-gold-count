@extends('layouts.app')

@section('title', 'Stock | ')

@section('main')
    <main class="app-main">
        <div class="app-content mt-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Hasil</h3>
                            </div>
                            <div class="card-body p-2">
                                @livewire('stock-invest')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
