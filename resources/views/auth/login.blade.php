@extends('layouts.app', [
    'namePage' => 'Login page',
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'login',
    'backgroundImage' => asset('assets') . "/img/DJI_0096.JPG",
])

@section('content')
    <div class="content">
        <div class="container">
        <div class="col-md-12 ml-auto mr-auto">
            <div class="header bg-gradient-primary py-10 py-lg-2 pt-lg-12">
                <div class="container">
                    <div class="header-body text-center mb-7">
                        <div class="row justify-content-center">
                            <div class="col-lg-12 col-md-9">
                                <p class="text-lead text-light mt-3 mb-0">
                                    @include('alerts.migrations_check')
                                </p>
                            </div>
                            <div class="col-lg-5 col-md-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 ml-auto mr-auto">
            <form role="form" method="POST" action="{{ route('login') }}">
                @csrf
            <div class="card card-login card-plain">
                <div class="card-header ">
                <div class="logo-container">
                    <img src="{{ asset('assets/imgDJI_0096.jpg') }}" alt="">
                </div>
                </div>
                <div class="card-body ">
                <div class="input-group no-border form-control-lg {{ $errors->has('email') ? ' has-danger' : '' }}">
                    <span class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="now-ui-icons users_circle-08"></i>
                    </div>
                    </span>
                    <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Enter your personal email') }}" type="email" name="email" value="" required autofocus>
                </div>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
                <div class="input-group no-border form-control-lg {{ $errors->has('password') ? ' has-danger' : '' }}">
                    <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="now-ui-icons objects_key-25"></i></i>
                    </div>
                    </div>
                    <input placeholder="Password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" type="password" value="" required>
                </div>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
                </div>
                <div class="card-footer ">
                <button  type = "submit" class="btn btn-primary btn-round btn-lg btn-block mb-3">{{ __('Login') }}</button>
                
                <!-- Microsoft Login Button -->
                <div class="text-center mb-3">
                    <p class="text-muted">- OR -</p>
                </div>
                <a href="{{ route('login.microsoft') }}" class="btn btn-info btn-round btn-lg btn-block mb-3">
                    <i class="fab fa-microsoft"></i> Login with Microsoft 365
                </a>
                
                {{-- <div class="pull-left">
                    <h6>
                    <a href="{{ route('register') }}" class="link footer-link">{{ __('Create Account') }}</a>
                    </h6>
                </div> --}}
                <div class="text-center">
                    <h6>
                        <a href="{{ route('password.request') }}" class="link footer-link"><p>Can't login? Click here to reset your Password</p></a>
                    </h6>                
                </div>
                </div>
            </div>
            </form>
        </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
        demo.checkFullPageBackgroundImage();
        });
    </script>
@endpush
