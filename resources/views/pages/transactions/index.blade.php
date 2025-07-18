@extends('layouts.app')

@section('title', 'Trasactions | ')

@section('main')
    <main class="app-main">
        <div class="app-content mt-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Transaksi</h3>
                            </div>
                            <div class="card-body p-2">
                                @livewire('transaction-crud')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.addEventListener('toast:success', event => {
            toastr.success(event.detail.message, 'Sukses!', {
                closeButton: true,
                progressBar: true
            });
        });

        window.addEventListener('toast:error', event => {
            toastr.error(event.detail.message, 'Gagal!', {
                closeButton: true,
                progressBar: true
            });
        });
    </script>
@endpush
