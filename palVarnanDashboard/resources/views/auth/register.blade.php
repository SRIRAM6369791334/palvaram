@extends('layouts.master-without_nav')

@section('title')
    @lang('translation.Register')
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
                                <img src="{{ URL::asset('assets/images/logoooo.png') }}" alt="" class="auth-logo-premium" style="width: 150px">
                            </a>
                        </div>

                        <div class="card login-card-glass">
                            <div class="card-body">
                                <div class="text-center mt-2">
                                    <h4 class="text-primary fw-bold">Create Account</h4>
                                    <p class="text-muted">Get your free Pal Varnan account now.</p>
                                </div>
                                <div class="p-2 mt-4">

                                    @if (Session::has('success'))
                                        <div class="alert alert-success text-center">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif

                                    <form method="POST" class="form-horizontal" action="{{ route('register') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="username">Username</label>
                                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" id="username" name="name" autofocus
                                                placeholder="Enter username">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="useremail">Email Address</label>
                                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                id="useremail" value="{{ old('email') }}" name="email"
                                                placeholder="name@example.com">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="userpassword">Password</label>
                                            <input type="password"
                                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                id="userpassword" name="password" placeholder="••••••••">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="password-confirm">Confirm Password</label>
                                            <input type="password"
                                                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                                id="confirmpassword" name="password_confirmation"
                                                placeholder="••••••••">
                                            @error('password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="avatar">Profile Picture</label>
                                            <input type="file"
                                                class="form-control @error('avatar') is-invalid @enderror"
                                                id="inputGroupFile02" name="avatar">
                                            @error('avatar')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-check mt-3">
                                            <input type="checkbox"
                                                class="form-check-input @error('checkbox') is-invalid @enderror"
                                                name="checkbox" id="auth-terms-condition-check">
                                            <label class="form-check-label" for="auth-terms-condition-check">I accept <a
                                                    href="javascript: void(0);" class="text-primary fw-semibold">Terms and
                                                    Conditions</a></label>
                                            @error('checkbox')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn login-btn-premium w-100 waves-effect waves-light"
                                                type="submit">Register</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <p class="text-muted mb-0">Already have an account ? <a
                                                    href="{{ url('login') }}" class="fw-bold text-primary"> Login</a>
                                            </p>
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
