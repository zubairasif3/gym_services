@extends('web.layouts.app')
@section('title', 'Privacy Policy')

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
                            <h2 class="text-white">Privacy Policy</h2>
                            <p class="text mb0 text-white">Learn how we collect, use, and protect your personal data.</p>
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
                        <h4>Privacy Policy</h4>
                        <p>FitScout ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and protect your information when you use our website, mobile application, or any related service (together, the "Service").</p>
                        <p>By using our Service, you agree to this Privacy Policy. If you do not agree, please do not use the Service.</p>

                        <h5>1. Information We Collect</h5>
                        <p>We may collect the following information:</p>
                        <ul>
                            <li><strong>Personal information:</strong> name, email address, phone number, billing data, and any other details you provide during registration or communication.</li>
                            <li><strong>Profile information:</strong> photos, service descriptions, skills, and qualifications you choose to display.</li>
                            <li><strong>Usage data:</strong> IP address, browser type, device information, pages visited, and actions on our platform.</li>
                            <li><strong>Transaction data:</strong> payment details (processed by third-party payment providers) and purchase history.</li>
                        </ul>

                        <h5>2. How We Use Your Information</h5>
                        <ul>
                            <li>Provide, operate, and improve the Service.</li>
                            <li>Connect users with sports professionals, gyms, and related service providers.</li>
                            <li>Process payments and send invoices.</li>
                            <li>Communicate updates, offers, and important changes.</li>
                            <li>Ensure compliance with our Terms of Service and legal requirements.</li>
                        </ul>

                        <h5>3. Sharing Your Information</h5>
                        <p>We do not sell your personal data. We may share it with:</p>
                        <ul>
                            <li><strong>Service providers:</strong> payment processors, hosting providers, analytics tools.</li>
                            <li><strong>Business partners:</strong> only when needed to provide the requested service.</li>
                            <li><strong>Legal authorities:</strong> if required by law or to protect our rights, safety, or property.</li>
                        </ul>

                        <h5>4. Data Retention</h5>
                        <p>We store your personal information only as long as necessary to provide the Service and for legal or business needs.</p>

                        <h5>5. Security</h5>
                        <p>We take reasonable steps to protect your information from unauthorized access, use, or disclosure. However, no online service is 100% secure.</p>

                        <h5>6. Your Rights</h5>
                        <p>Depending on your location, you may have the right to:</p>
                        <ul>
                            <li>Access, update, or delete your personal data.</li>
                            <li>Restrict or object to the processing of your data.</li>
                            <li>Withdraw consent at any time when processing is based on consent.</li>
                        </ul>
                        <p>You can contact us to exercise these rights: <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>

                        <h5>7. Third-Party Links</h5>
                        <p>The Service may contain links to third-party websites or services. We are not responsible for their privacy practices.</p>

                        <h5>8. Changes to This Policy</h5>
                        <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with a new effective date.</p>

                        <h5>9. Contact</h5>
                        <p>For any questions about this Privacy Policy, contact us at: <a href="mailto:info@fitscout.it">info@fitscout.it</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
