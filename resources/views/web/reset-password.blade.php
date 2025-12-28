@extends('web.layouts.app')
@section('title', 'Reset Password')

@section('content')
    <div style="padding-top: 78px;"></div>
    <!-- Reset Password Area -->
    <section class="our-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                    <div class="main-title text-center">
                        <h2 class="title">Reset Password</h2>
                        <p class="paragraph">Enter your new password to complete the reset process.</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                
                <div class="row wow fadeInRight" data-wow-delay="300ms">
                    <div class="col-xl-6 mx-auto">
                        <div class="log-reg-form search-modal form-style1 bgc-white p50 p30-sm default-box-shadow1 bdrs12">
                            @if ($errors->any())
                                <div class="alert alert-danger mb20" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif

                            <div class="mb30">
                                <h4>Create New Password</h4>
                                <p class="text">Your new password must be different from previously used passwords.</p>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="alitfn58@gmail.com" value="{{ $email ?? old('email') }}" required readonly>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="*******" required autofocus>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="*******" required>
                            </div>
                            <div class="d-grid mb20">
                                <button class="ud-btn btn-thm" type="submit">Reset Password <i class="fal fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection

