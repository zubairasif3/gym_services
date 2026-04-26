@extends('web.layouts.app')
@section('title', __('web.forgot_password.title'))

@section('content')
    {{-- <div style="padding-top: 78px;"></div> --}}
    <!-- Forgot Password Area -->
    <section class="our-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
                    <div class="main-title text-center">
                        <h2 class="title">{{ __('web.forgot_password.title') }}</h2>
                        <p class="paragraph">{{ __('web.forgot_password.subtitle') }}</p>
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
                                <h4>{{ __('web.forgot_password.heading') }}</h4>
                                <p class="text">{{ __('web.forgot_password.remember') }} <a href="{{ route('web.login') }}" class="text-thm">{{ __('web.login.button') }}!</a></p>
                            </div>
                            <div class="mb20">
                                <label class="form-label fw600 dark-color">{{ __('web.login.email') }}</label>
                                <input type="email" name="email" class="form-control" placeholder="alitfn58@gmail.com" value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="d-grid mb20">
                                <button class="ud-btn btn-thm" type="submit">{{ __('web.forgot_password.send_link') }} <i class="fal fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection

