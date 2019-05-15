@extends('layouts.auth')

@section('title')
    <title>Login</title>
@endsection

@section('content')
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form method="POST" action="{{ route('login') }}">
                {{csrf_field()}}

                @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                    {!! session('error') !!}
                            </div>
                @endif
                
                <div class="row">
                    <div class="col-1">
                <span class="fa fa-envelope form-control-feedback"> {{ $errors->first('username') }}</span> 
                    </div>            
                    <div class="col-11">
                <div class="form-group has-feedback">
                    <input 
                        type="text"
                        name="username" 
                        class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" 
                        placeholder="{{ __('Username') }}"
                        >                    
                </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-1">
                            <span class="fa fa-lock form-control-feedback"> {{ $errors->first('password') }}</span>
                    </div>
                    <div class="col-11">
                <div class="form-group has-feedback">
                    <input type="password" 
                        name="password"
                        class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }} " 
                        placeholder="{{ __('Password') }}">
                    {{-- <span class="fa fa-lock form-control-feedback"> {{ $errors->first('password') }}</span> --}}
                </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>

            {{-- <div class="social-auth-links text-center mb-3">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fa fa-facebook mr-2"></i> Sign in using Facebook
                </a>
                <a href="#" class="btn btn-block btn-danger">
                    <i class="fa fa-google-plus mr-2"></i> Sign in using Google+
                </a>
            </div>

            <p class="mb-1">
                <a href="#">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="#" class="text-center">Register a new membership</a>
            </p> --}}
        </div>
    </div>
@endsection