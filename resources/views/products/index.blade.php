@extends('layouts.master')

@section('title')
    <title>Manajemen Produk</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Manajemen Produk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Produk</li>
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
                                <h3 class="card-title">List Produk</h3>
                            </div>
                            <div class="card-body">
                           
                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                    {!! session('success') !!}
                            </div>
                            @endif
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Stok</th>
                                            <th>Harga</th>
                                            <th>Kategori</th>
                                            <th>Last Update</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($products as $row)
                                        <tr>
                                            <td>
                                                @if (!empty($row->photo))
                                                    <img src="{{ asset('uploads/product/' . $row->photo) }}" 
                                                        alt="{{ $row->name }}" width="50px" height="50px">
                                                @else
                                                    <img src="http://via.placeholder.com/50x50" alt="{{ $row->name }}">
                                                @endif
                                            </td>
                                            <td>
                                                    <strong>{{ $row->code }}</strong>
                                            </td>
                                            <td>
                                                {{ ucfirst($row->name) }}
                                            </td>
                                            <td>{{ $row->stock }}</td>
                                            <td>Rp {{ number_format($row->price) }}</td>
                                            <td>{{ $row->category->name }}</td>
                                            <td>{{ $row->updated_at }}</td>
                                            <td>
                                                <form action="{{ route('produk.destroy', $row->id) }}" method="POST">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <a href="{{ route('produk.edit', $row->id) }}" 
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <a href="{{ route('produk.create') }}" 
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Tambah
                            </a>
                            </div>
                            <div class="float-right">
                                {!! $products->links() !!}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection