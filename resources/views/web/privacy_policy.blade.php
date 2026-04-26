@extends('web.layouts.app')
@section('title', __('web.privacy_policy_page.title'))

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
                            <h2 class="text-white">{{ __('web.privacy_policy_page.title') }}</h2>
                            <p class="text mb0 text-white">{{ __('web.privacy_policy_page.subtitle') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Privacy Policy Content -->
    <section class="pt60 pb60">
        <div class="container">
            <div class="row justify-content-center wow fadeInUp" data-wow-delay="300ms">
                <div class="col-lg-10">
                    <div class="terms-content">
                        <h4>{{ __('web.privacy_policy_page.title') }}</h4>
                        <p>{{ __('web.privacy_policy_page.intro_1') }}</p>
                        <p>{{ __('web.privacy_policy_page.intro_2') }}</p>

                        <h5>{{ __('web.privacy_policy_page.collect_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.collect_intro') }}</p>
                        <ul>
                            <li><strong>{{ __('web.privacy_policy_page.personal_info') }}</strong> {{ __('web.privacy_policy_page.personal_info_text') }}</li>
                            <li><strong>{{ __('web.privacy_policy_page.profile_info') }}</strong> {{ __('web.privacy_policy_page.profile_info_text') }}</li>
                            <li><strong>{{ __('web.privacy_policy_page.usage_data') }}</strong> {{ __('web.privacy_policy_page.usage_data_text') }}</li>
                            <li><strong>{{ __('web.privacy_policy_page.transaction_data') }}</strong> {{ __('web.privacy_policy_page.transaction_data_text') }}</li>
                        </ul>

                        <h5>{{ __('web.privacy_policy_page.use_title') }}</h5>
                        <ul>
                            <li>{{ __('web.privacy_policy_page.use_1') }}</li>
                            <li>{{ __('web.privacy_policy_page.use_2') }}</li>
                            <li>{{ __('web.privacy_policy_page.use_3') }}</li>
                            <li>{{ __('web.privacy_policy_page.use_4') }}</li>
                            <li>{{ __('web.privacy_policy_page.use_5') }}</li>
                        </ul>

                        <h5>{{ __('web.privacy_policy_page.sharing_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.sharing_intro') }}</p>
                        <ul>
                            <li><strong>{{ __('web.privacy_policy_page.service_providers') }}</strong> {{ __('web.privacy_policy_page.service_providers_text') }}</li>
                            <li><strong>{{ __('web.privacy_policy_page.business_partners') }}</strong> {{ __('web.privacy_policy_page.business_partners_text') }}</li>
                            <li><strong>{{ __('web.privacy_policy_page.legal_authorities') }}</strong> {{ __('web.privacy_policy_page.legal_authorities_text') }}</li>
                        </ul>

                        <h5>{{ __('web.privacy_policy_page.retention_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.retention_text') }}</p>

                        <h5>{{ __('web.privacy_policy_page.security_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.security_text') }}</p>

                        <h5>{{ __('web.privacy_policy_page.rights_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.rights_intro') }}</p>
                        <ul>
                            <li>{{ __('web.privacy_policy_page.rights_1') }}</li>
                            <li>{{ __('web.privacy_policy_page.rights_2') }}</li>
                            <li>{{ __('web.privacy_policy_page.rights_3') }}</li>
                        </ul>
                        <p>{{ __('web.privacy_policy_page.rights_contact') }} <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>

                        <h5>{{ __('web.privacy_policy_page.links_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.links_text') }}</p>

                        <h5>{{ __('web.privacy_policy_page.changes_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.changes_text') }}</p>

                        <h5>{{ __('web.privacy_policy_page.contact_title') }}</h5>
                        <p>{{ __('web.privacy_policy_page.contact_text') }} <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
