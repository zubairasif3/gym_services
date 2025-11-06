@extends('web.layouts.app')
@section('title', 'Verify Email - FitScout')

@section('content')
<div style="padding-top: 78px;"></div>
<section class="our-register" style="padding: 60px 0; background: #f5f5f5;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="main-title text-center">
                    <h2 class="title">Verify your email address</h2>
                    <p class="paragraph">We have sent a verification email to your address.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="log-reg-form search_area" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    @if (session('registered'))
                        <div class="alert alert-success" role="alert">
                            <strong>Registration completed!</strong> We have sent a verification email to your address. Check your email and click on the link to verify your account.
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-4">
                        <p style="font-size: 16px; line-height: 1.8; color: #333;">
                                Before continuing, check your email for the verification link.
                            If you didn't receive the email, we can send you another one.
                        </p>
                    </div>

                    @if (session('email'))
                        <div class="mb-3">
                            <p style="font-size: 14px; color: #666;">
                                Email sent to: <strong>{{ session('email') }}</strong>
                            </p>
                        </div>
                    @endif

                    @auth
                        @if (!Auth::user()->hasVerifiedEmail())
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" style="padding: 12px 20px; background: #00b3f1; border: none; border-radius: 4px; color: white; font-weight: 600;">
                                    Send verification email again
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-info" role="alert" style="margin-top: 20px;">
                            <p style="margin: 0; font-size: 14px;">
                                If you have just registered an account, check your email for the verification link.
                                After verifying the email, you can login.
                            </p>
                        </div>
                    @endauth

                    <div class="mt-4 text-center">
                        <a href="{{ route('web.login') }}" style="color: #00b3f1; text-decoration: none;">
                            Go to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

