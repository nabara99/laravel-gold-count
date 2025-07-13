@extends('layouts.app')

@section('title', 'Users | ')

@section('main')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Users</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                                <h3 class="card-title">Daftar Users</h3>
                            </div>
                            <div class="card-body p-2">
                                <div class="mb-1 d-flex">
                                    <form method="GET" action="{{ route('user.index') }}" class="d-flex ms-auto">
                                        <input type="text" name="search" class="form-control" placeholder="Cari user..."
                                            value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-warning"><i class="bi bi-search"></i></button>
                                    </form>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 15px">No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th style="width: 50px">#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <tr class="align-middle">
                                                    <td>{{ $users->firstItem() + $loop->index }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        {{ $user->role?->role_name }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#roleModal{{ $user->id }}"
                                                            title="change role">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    </td>

                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No users found</td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer clearfix">
                                <div class="float-end">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($users as $user)
                <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Role {{ $user->name }}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('user.update-role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <div>
                                        <label for="role_id" class="mb-2">Pilih role akses</label>
                                        <select name="role_id" id="role_id" class="form-control">
                                            <option value="">{{ $user->role->role_name }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    @if ($user->role_id == $role->id) disabled @endif>
                                                    {{ $role->role_name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary mt-2">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </main>

@endsection
