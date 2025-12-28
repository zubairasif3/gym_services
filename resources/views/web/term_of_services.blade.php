@extends('web.layouts.app')
@section('title', 'Terms of Service')

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
                            <h2 class="text-white">Terms of Service</h2>
                            <p class="text mb0 text-white">Please read our Terms of Service carefully before using FitScout.</p>
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
                        <h4>Terms of Service</h4>
                        <p>These Terms of Service ("Terms") govern your use of FitScout ("we", "our", or "us") services, including our website, mobile application, and related features (together, the "Service").</p>
                        <p>By using the Service, you agree to these Terms. If you do not agree, do not use the Service.</p>

                        <h5>1. Use of the Service</h5>
                        <p>You must be at least 18 years old to use the Service. You agree to use it only for lawful purposes and in compliance with these Terms.</p>

                        <h5>2. User Account</h5>
                        <p>To use certain features, you may need to create an account. You must provide accurate information and keep your login details secure. You are responsible for all activities under your account.</p>

                        <h5>3. Content You Share</h5>
                        <p>You are responsible for the content you post on the Service. You must not post illegal, harmful, or infringing content.</p>
                        <p>By posting content, you grant us a license to use, display, and distribute it to operate the Service.</p>

                        <h5>4. Payments</h5>
                        <p>Some features of the Service may require payment. Payments are handled by third-party providers. You agree to provide valid payment information and authorize charges as needed.</p>

                        <h5>5. Prohibited Activities</h5>
                        <ul>
                            <li>Misuse the Service or attempt to hack it.</li>
                            <li>Post spam, scams, or fraudulent offers.</li>
                            <li>Impersonate another person or entity.</li>
                        </ul>

                        <h5>6. Termination</h5>
                        <p>We may suspend or close your account if you violate these Terms or misuse the Service.</p>

                        <h5>7. Liability</h5>
                        <p>The Service is provided "as is" without warranties. We are not liable for damages resulting from your use of the Service, except as required by law.</p>

                        <h5>8. Changes to the Terms</h5>
                        <p>We may update these Terms from time to time. The new version will be posted on this page with the updated effective date.</p>

                        <h5>9. Contact</h5>
                        <p>For questions about these Terms, contact us at: <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
