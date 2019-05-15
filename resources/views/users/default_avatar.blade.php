@extends('layouts.master')

@section('title')
    <title>Set Default Avatar</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Set Default Avatar</h1>
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

                                    @if (session('success'))
                                    <div class="alert alert-success alert-dismissible">
                                            {!! session('success') !!}
                                    </div>
                                    @endif
                            
                            <form action="{{ route('users.setDefaultAvatar') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PUT">
                                <div class="form-group">
                                    
                                    <input type="file" name="photo" class="form-control">
                                    <p class="text-danger">{{ $errors->first('photo') }}</p>  
                                    <label for="">Default Avatar Sekarang</label>                                
                                        <hr>
                                        <img src="{{ asset('uploads/profile/profile.png') }}" 
                                            {{-- alt="{{ $user->name }}" --}}
                                            width="150px" height="150px">                                    
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fa fa-send"></i> Update
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