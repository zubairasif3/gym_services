@extends('web.layouts.app')
@section('title', __('web.verify_email.title'))

@section('content')
<div style="padding-top: 78px;"></div>
<section class="our-register" style="padding: 60px 0; background: #f5f5f5;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="main-title text-center">
                    <h2 class="title">{{ __('web.verify_email.heading') }}</h2>
                    <p class="paragraph">{{ __('web.verify_email.subtitle') }}</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="log-reg-form search_area" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    @if (session('registered'))
                        <div class="alert alert-success" role="alert">
                            <strong>{{ __('web.verify_email.registered') }}</strong> {{ __('web.verify_email.registered_message') }}
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
                            {!! __('web.verify_email.instructions', ['folder' => '<strong>' . __('web.verify_email.spam_folder') . '</strong>']) !!}
                        </p>
                    </div>

                    @if (session('email'))
                        <div class="mb-3">
                            <p style="font-size: 14px; color: #666;">
                                {{ __('web.verify_email.email_sent_to') }} <strong>{{ session('email') }}</strong>
                            </p>
                        </div>
                    @endif

                    @auth
                        @if (!Auth::user()->hasVerifiedEmail())
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" style="padding: 12px 20px; background: #00b3f1; border: none; border-radius: 4px; color: white; font-weight: 600; margin-bottom: 15px;">
                                    {{ __('web.verify_email.resend') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <form method="POST" action="{{ route('verification.send.guest') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <button type="submit" class="btn btn-primary w-100" style="padding: 12px 20px; background: #00b3f1; border: none; border-radius: 4px; color: white; font-weight: 600; margin-bottom: 15px;">
                                {{ __('web.verify_email.resend') }}
                            </button>
                        </form>
                    @endauth

                    <div class="alert alert-info" role="alert" style="margin-top: 20px;">
                        <p style="margin: 0; font-size: 14px;">
                            {{ __('web.verify_email.info') }}
                        </p>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('web.login') }}" style="color: #00b3f1; text-decoration: none;">
                            {{ __('web.verify_email.go_login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

