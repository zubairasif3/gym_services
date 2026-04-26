@extends('web.layouts.app')
@section('title', __('web.terms_page.title'))

@section('content')
    {{-- <div style="padding-top: 78px;"></div> --}}

    <!-- Breadcrumb Section -->
    <section class="breadcumb-section wow fadeInUp">
        <div class="cta-commmon-v1 cta-banner bgc-thm2 mx-auto maxw1700 pt120 pb120 bdrs16 position-relative overflow-hidden d-flex align-items-center mx20-lg">
            <img class="left-top-img wow zoomIn" src="{{ asset('web/images/vector-img/left-top.png') }}" alt="">
            <img class="right-bottom-img wow zoomIn" src="{{ asset('web/images/vector-img/right-bottom.png') }}" alt="">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="position-relative wow fadeInUp" data-wow-delay="300ms">
                            <h2 class="text-white">{{ __('web.terms_page.title') }}</h2>
                            <p class="text mb0 text-white">{{ __('web.terms_page.subtitle') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Terms of Service Content -->
    <section class="pt60 pb60">
        <div class="container">
            <div class="row justify-content-center wow fadeInUp" data-wow-delay="300ms">
                <div class="col-lg-10">
                    <div class="terms-content">
                        <h4>{{ __('web.terms_page.title') }}</h4>
                        <p>{{ __('web.terms_page.intro_1') }}</p>
                        <p>{{ __('web.terms_page.intro_2') }}</p>

                        <h5>{{ __('web.terms_page.use_title') }}</h5>
                        <p>{{ __('web.terms_page.use_text') }}</p>

                        <h5>{{ __('web.terms_page.account_title') }}</h5>
                        <p>{{ __('web.terms_page.account_text') }}</p>

                        <h5>{{ __('web.terms_page.content_title') }}</h5>
                        <p>{{ __('web.terms_page.content_text_1') }}</p>
                        <p>{{ __('web.terms_page.content_text_2') }}</p>

                        <h5>{{ __('web.terms_page.payments_title') }}</h5>
                        <p>{{ __('web.terms_page.payments_text') }}</p>

                        <h5>{{ __('web.terms_page.prohibited_title') }}</h5>
                        <ul>
                            <li>{{ __('web.terms_page.prohibited_1') }}</li>
                            <li>{{ __('web.terms_page.prohibited_2') }}</li>
                            <li>{{ __('web.terms_page.prohibited_3') }}</li>
                        </ul>

                        <h5>{{ __('web.terms_page.termination_title') }}</h5>
                        <p>{{ __('web.terms_page.termination_text') }}</p>

                        <h5>{{ __('web.terms_page.liability_title') }}</h5>
                        <p>{{ __('web.terms_page.liability_text') }}</p>

                        <h5>{{ __('web.terms_page.changes_title') }}</h5>
                        <p>{{ __('web.terms_page.changes_text') }}</p>

                        <h5>{{ __('web.terms_page.contact_title') }}</h5>
                        <p>{{ __('web.terms_page.contact_text') }} <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
