@extends('web.layouts.app')
@section('title', 'Home Page')

@section('content')
    {{-- <div style="padding-top: 78px;"></div> --}}
    <!-- Our LogIn Area -->
    <section class="our-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                    <div class="main-title text-center">
                        <h2 class="title">Log In</h2>
                        <p class="paragraph">Login your account to access personalized services.</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('web.login.post') }}" method="POST">
                @csrf
                <div class="row wow fadeInRight" data-wow-delay="300ms">
                    <div class="col-xl-6 mx-auto">
                        <div class="log-reg-form search-modal form-style1 bgc-white p50 p30-sm default-box-shadow1 bdrs12">
                            @if (session('status'))
                                <div class="alert alert-success mb20" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            
                            @if ($errors->any())
                                <div class="alert alert-danger mb20" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif

                            <div class="mb30">
                                <h4>We're glad to see you again!</h4>
                                <p class="text">Don't have an account? <a href="{{ route('web.register') }}" class="text-thm">Sign Up!</a></p>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="alitfn58@gmail.com" required>
                            </div>
                            <div class="mb15">
                                <label class="form-label fw600 dark-color">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="*******" required>
                            </div>
                            <div class="checkbox-style1 d-block d-sm-flex align-items-center justify-content-between mb20">
                                <label class="custom_checkbox fz14 ff-heading"></label>
                                <a class="fz14 ff-heading" href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                            <div class="d-grid mb20">
                                <button class="ud-btn btn-thm" type="submit">Log In <i class="fal fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection
