@extends('layouts.master')

@section('title')
    <title>Add New Users</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Add New Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">User</a></li>
                            <li class="breadcrumb-item active">Add New</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                            <div class="card">
                                    <div class="card-header with-border">
                                    </div>
                                    <div class="card-body">
                            
                            @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                    {!! session('error') !!}
                            </div>
                            @endif
                            
                            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="">Nama</label>
                                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid':'' }}" required>
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                        <label for="">Username</label>
                                        <input type="text" name="username" class="form-control {{ $errors->has('username') ? 'is-invalid':'' }}" required>
                                        <p class="text-danger">{{ $errors->first('username') }}</p>
                                    </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid':'' }}" required>
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="number" name="password" placeholder="Hanya bisa angka" class="form-control {{ $errors->has('password') ? 'is-invalid':'' }}" required>
                                    <p class="text-danger">{{ $errors->first('password') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="">Role</label>
                                    <select name="role" class="form-control {{ $errors->has('role') ? 'is-invalid':'' }}" required>
                                        <option value="">Pilih</option>
                                        @foreach ($role as $row)
                                        <option value="{{ $row->name }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('role') }}</p>
                                </div>
                                <div class="form-group">
                                        <label for="">Foto</label>
                                        <input type="file" name="photo" class="form-control">
                                        <p class="text-danger">{{ $errors->first('photo') }}</p>
                                    </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fa fa-send"></i> Simpan
                                    </button>
                                </div>
                            </form>
                                    </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection