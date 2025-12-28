@extends('web.layouts.app')
@section('title', 'Forgot Password')

@section('content')
    <div style="padding-top: 78px;"></div>
    <!-- Forgot Password Area -->
    <section class="our-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                    <div class="main-title text-center">
                        <h2 class="title">Forgot Password</h2>
                        <p class="paragraph">Enter your email address and we'll send you a link to reset your password.</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('password.email') }}" method="POST">
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
                                <h4>Reset Your Password</h4>
                                <p class="text">Remember your password? <a href="{{ route('web.login') }}" class="text-thm">Log In!</a></p>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="alitfn58@gmail.com" value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="d-grid mb20">
                                <button class="ud-btn btn-thm" type="submit">Send Password Reset Link <i class="fal fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection

