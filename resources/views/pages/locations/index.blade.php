@extends('layouts.app')

@section('title', 'Locations | ')

@section('main')
<main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Locations</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Locations</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Lokasi</h3>
                            </div>
                            <div class="card-body p-2">
                                @livewire('location-crud')
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

