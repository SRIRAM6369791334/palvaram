@extends('layouts.master-without_nav')

@section('title')
   Pal Varnan | @lang('translation.Login')
@endsection

@section('content')
    <div class="authentication-bg min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="container">
            <div class="d-flex flex-column min-vh-100 px-3 pt-4">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-lg-6 col-xl-5">

                        <div class="text-center mb-4">
                            <a href="/">
                                <!-- <img src="{{ URL::asset('assets/images/logoooo.png') }}" alt="" class="auth-logo-premium" style="width: 150px"> -->
                            </a>
                        </div>

                        <div class="card login-card-glass">
                            <div class="card-body">
                                <div class="text-center mt-2">
                                    <h4 class="text-primary fw-bold">Welcome Back !</h4>
                                    <p class="text-muted">Sign in to continue to Pal Varnan.</p>
                                </div>
                                <div class="p-2 mt-4">
                                    @if (Session::has('success'))
                                        <div class="alert alert-success text-center">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="username">Email Address</label>
                                            <input name="email" type="email" id="useremail"
                                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" placeholder="name@example.com" autocomplete="email"
                                                autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="userpassword">Password</label>
                                            <div class="password-input-container">
                                                <input type="password" name="password"
                                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                    id="userpassword" value="" data-toggle="password"
                                                    placeholder="••••••••" aria-label="Password"
                                                    aria-describedby="password-addon" maxlength="10">
                                                <span class="password-toggle-icon" title="Show/Hide Password">
                                                    <i class="fa fa-fw fa-eye password-toggle-icon-show text-muted"></i>
                                                    <i class="fa fa-fw fa-eye-slash password-toggle-icon-hide text-muted"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mt-4 text-center">
                                            <button class="btn login-btn-premium w-100 waves-effect waves-light" type="submit">
                                                Log In
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center p-4" style="color:rgba(255,255,255,0.8); position: relative; z-index: 10;">
                            <p class="mb-0">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Pal Varnan. Developed with <i class="mdi mdi-heart text-danger"></i> by
                                Sai Techno Solutions.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- end container -->
    </div>
@endsection


@section('script')
    <script src="{{ URL::asset('assets/js/app/LoginPage.js') }}"></script>
@endsection
