@extends('web.layouts.app')
@section('title', 'About Us')

@section('content')
   <!-- Breadcumb Sections -->
    <section class="breadcumb-section">
      <div class="cta-about-v1 mx-auto maxw1700 pt120 pb120 bdrs16 position-relative overflow-hidden d-flex align-items-center mx20-lg">
        <div class="container">
          <div class="row">
            <div class="col-xl-5">
              <div class="position-relative">
                <h2 class="text-white">About FitScout</h2>
                <p class="text-white mb30">Turn your skills into opportunities</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section Area -->
    <section class="our-about pb0 pt60-lg">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 col-xl-6">
            <div class="about-img mb30-sm wow fadeInRight" data-wow-delay="300ms">
              <div
                class="w100 bdrs16"
                style="min-height: 420px; background-size: cover; background-position: center; background-repeat: no-repeat; background-image: linear-gradient(rgba(0,0,0,.18), rgba(0,0,0,.18)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80');"
                aria-label="Fitness training"
              ></div>
            </div>
          </div>
          <div class="col-md-6 col-xl-5 offset-xl-1">
            <div class="position-relative wow fadeInLeft" data-wow-delay="300ms">
              <h2 class="mb25">Your Premier Marketplace for <br class="d-none d-xl-block"> Fitness & Wellness Professionals</h2>
              <p class="text mb25">FitScout is the platform that connects customers and professionals by offering a unique and complete experience</p>
              <div class="list-style2">
                <ul class="mb20">
                  <li><i class="far fa-check"></i>Discover the best fitness, health and sports experts, the most active and followed by the community.</li>
                  <li><i class="far fa-check"></i>Follow, stay connected, and stay in touch with the professionals you love.</li>
                  <li><i class="far fa-check"></i>Get in direct contact with them and start your wellness path, in a simple and immediate way.</li>
                </ul>
              </div>
              <a href="{{ route('web.services') }}" class="ud-btn btn-thm-border">Find Your Trainer<i class="fal fa-arrow-right-long"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Funfact -->
    <section class="pb0 pt60">
      <div class="container maxw1600 bdrb1 pb60">
        <div class="row justify-content-center wow fadeInUp" data-wow-delay="300ms">
          <div class="col-6 col-md-3">
            <div class="funfact_one text-center">
              <div class="details">
                <ul class="ps-0 mb-0 d-flex justify-content-center">
                  <li><div class="timer">834</div></li>
                  <li><span>M</span></li>
                </ul>
                <p class="text mb-0">Fitness Professionals</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="funfact_one text-center">
              <div class="details">
                <ul class="ps-0 mb-0 d-flex justify-content-center">
                  <li><div class="timer">732</div></li>
                  <li><span>M</span></li>
                </ul>
                <p class="text mb-0">5-Star Reviews</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="funfact_one text-center">
              <div class="details">
                <ul class="ps-0 mb-0 d-flex justify-content-center">
                  <li><div class="timer">90</div></li>
                  <li><span>M</span></li>
                </ul>
                <p class="text mb-0">Sessions Booked</p>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="funfact_one text-center">
              <div class="details">
                <ul class="ps-0 mb-0 d-flex justify-content-center">
                  <li><div class="timer">236</div></li>
                  <li><span>M</span></li>
                </ul>
                <p class="text mb-0">Goals Achieved</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="p-0">
      <div class="cta-banner mx-auto maxw1600 pt120 pt60-lg pb90 pb60-lg position-relative overflow-hidden mx20-lg">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6 col-xl-5 pl30-md pl15-xs wow fadeInRight" data-wow-delay="500ms">
              <div class="mb30">
                <div class="main-title">
                  <h2 class="title">A whole world of professionals at your disposal</h2>
                </div>
              </div>
              <div class="why-chose-list">
                <div class="list-one d-flex align-items-start mb30">
                  <span class="list-icon flex-shrink-0 flaticon-badge"></span>
                  <div class="list-content flex-grow-1 ml20">
                    <h4 class="mb-1">Find the right professional</h4>
                    <p class="text mb-0 fz15">Search for the professional that best suits your needs, compare them with each other thanks to the reviews of other customers.</p>
                  </div>
                </div>
                <div class="list-one d-flex align-items-start mb30">
                  <span class="list-icon flex-shrink-0 flaticon-money"></span>
                  <div class="list-content flex-grow-1 ml20">
                    <h4 class="mb-1">Totally free for customers</h4>
                    <p class="text mb-0 fz15">The use of the platform for customers is totally free.</p>
                  </div>
                </div>
                <div class="list-one d-flex align-items-start mb30">
                  <span class="list-icon flex-shrink-0 flaticon-security"></span>
                  <div class="list-content flex-grow-1 ml20">
                    <h4 class="mb-1">Safe and secure</h4>
                    <p class="text mb-0 fz15">Travel safely within the platform, we will protect your data and your privacy. We will be at your disposal if you need it.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-6 offset-xl-1 wow fadeInLeft" data-wow-delay="500ms">
              <div class="about-img">
                <div
                  class="w100 bdrs16"
                  style="min-height: 420px; background-size: cover; background-position: center; background-repeat: no-repeat; background-image: linear-gradient(rgba(0,0,0,.18), rgba(0,0,0,.18)), url('https://images.unsplash.com/photo-1549576490-b0b4831ef60a?auto=format&fit=crop&w=1200&q=80');"
                  aria-label="Wellness coaching"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Funfact -->
    <section class="bgc-light-yellow pb90 pb30-md overflow-hidden maxw1700 mx-auto bdrs4">
      <img class="left-top-img wow zoomIn d-none d-lg-block" src="{{ asset('web/images/vector-img/left-top.png') }}" alt="">
      <img class="right-bottom-img wow zoomIn d-none d-lg-block" src="{{ asset('web/images/vector-img/right-bottom.png') }}" alt="">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 col-xl-4 offset-xl-1 wow fadeInRight" data-wow-delay="100ms">
            <div class="cta-style6 mb30-sm">
              <h2 class="cta-title mb25">Find the professional  <br class="d-none d-lg-block"> that best suits your needs</h2>
              <p class="text-thm2 fz15 mb25">We give importance to your well-being and your health, which is why
                we have chosen to offer you the best professionals.</p>
              <a href="{{ route('web.contact') }}" class="ud-btn btn-thm">Get Started <i class="fal fa-arrow-right-long"></i></a>
            </div>
          </div>
          <div class="col-md-6 col-xl-6 offset-xl-1 wow fadeInLeft" data-wow-delay="300ms">
            <div class="row g-3">
              <div class="col-sm-6">
                <a href="{{ route('web.services') }}" class="text-decoration-none">
                  <div class="feature-style1 bdrs16">
                    <div class="feature-img bdrs16 overflow-hidden">
                      <div
                        class="w-100"
                        style="min-height: 190px; background-size: cover; background-position: center; background-repeat: no-repeat; background-image: linear-gradient(rgba(0,0,0,.18), rgba(0,0,0,.18)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=900&q=80');"
                        aria-label="Fitness & Strength"
                      ></div>
                    </div>
                    <div class="feature-content">
                      <div class="top-area">
                        <h6 class="title mb-1">Explore</h6>
                        <h5 class="text">Fitness & Strength</h5>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-sm-6">
                <a href="{{ route('web.services') }}" class="text-decoration-none">
                  <div class="feature-style1 bdrs16">
                    <div class="feature-img bdrs16 overflow-hidden">
                      <div
                        class="w-100"
                        style="min-height: 190px; background-size: cover; background-position: center; background-repeat: no-repeat; background-image: linear-gradient(rgba(0,0,0,.18), rgba(0,0,0,.18)), url('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=900&q=80');"
                        aria-label="Yoga & Mobility"
                      ></div>
                    </div>
                    <div class="feature-content">
                      <div class="top-area">
                        <h6 class="title mb-1">Explore</h6>
                        <h5 class="text">Yoga & Mobility</h5>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Testimonials -->
    <section class="our-testimonial">
      <div class="container wow fadeInUp" data-wow-delay="300ms">
        <div class="row">
          <div class="col-lg-6 m-auto">
            <div class="main-title text-center">
              <h2 class="title">What our customers say</h2>
              <p class="paragraph mt10">Discover the perfect service for you thanks to the reviews</p>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-xl-10 mx-auto">
            <div class="home2_testimonial_tabs position-relative">
              <div class="tab-content" id="pills-tabContent2">
                @php
                  $aboutTestimonials = $testimonials ?? collect();
                @endphp

                @forelse($aboutTestimonials as $t)
                  <div class="tab-pane fade {{ !empty($t['active']) ? 'show active' : '' }}" id="{{ $t['id'] }}" role="tabpanel" aria-labelledby="{{ $t['id'] }}-tab">
                    <div class="testimonial-style2 at-about2 text-center">
                      <div class="testi-content text-center">
                        <span class="icon fas fa-quote-left"></span>
                        <h4 class="testi-text">{{ $t['text'] }}</h4>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="tab-pane fade show active" id="testimonial-0" role="tabpanel" aria-labelledby="testimonial-0-tab">
                    <div class="testimonial-style2 at-about2 text-center">
                      <div class="testi-content text-center">
                        <span class="icon fas fa-quote-left"></span>
                        <h4 class="testi-text">“FitScout made it easy to compare professionals and book with confidence. Great experience overall.”</h4>
                      </div>
                    </div>
                  </div>
                @endforelse
              </div>
              <ul class="nav justify-content-center" id="pills-tab2" role="tablist">
                @forelse($aboutTestimonials as $t)
                  <li class="nav-item" role="presentation">
                    <a
                      class="nav-link {{ !empty($t['active']) ? 'active' : '' }}"
                      id="{{ $t['id'] }}-tab"
                      data-bs-toggle="pill"
                      href="#{{ $t['id'] }}"
                      role="tab"
                      aria-controls="{{ $t['id'] }}"
                      aria-selected="{{ !empty($t['active']) ? 'true' : 'false' }}"
                    >
                      <div class="thumb d-flex align-items-center">
                        <img class="rounded-circle" src="{{ asset($t['avatar']) }}" alt="{{ $t['name'] }}">
                        <h6 class="title ml30 ml15-xl mb-0">{{ $t['name'] }}<br><small>{{ $t['role'] }}</small></h6>
                      </div>
                    </a>
                  </li>
                @empty
                  <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="testimonial-0-tab" data-bs-toggle="pill" href="#testimonial-0" role="tab" aria-controls="testimonial-0" aria-selected="true">
                      <div class="thumb d-flex align-items-center">
                        <img class="rounded-circle" src="{{ asset('web/images/testimonials/1.jpg') }}" alt="Customer">
                        <h6 class="title ml30 ml15-xl mb-0">Customer<br><small>Review</small></h6>
                      </div>
                    </a>
                  </li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- <!-- Faq Area -->
    <section class="our-faq pb90">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 m-auto wow fadeInUp" data-wow-delay="300ms">
            <div class="main-title text-center">
              <h2 class="title">Frequently Asked Questions</h2>
              <p class="paragraph mt10">Lorem ipsum dolor sit amet, consectetur.</p>
            </div>
          </div>
        </div>
        <div class="row wow fadeInUp" data-wow-delay="300ms">
          <div class="col-xl-8 mx-auto">
            <div class="ui-content">
              <div class="accordion-style1 faq-page mb-4 mb-lg-5">
                <div class="accordion" id="accordionExample">
                  <div class="accordion-item active">
                    <h2 class="accordion-header" id="headingOne">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">What methods of payments are supported?</button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                      <div class="accordion-body">Cras vitae ac nunc orci. Purus amet tortor non at phasellus ultricies hendrerit. Eget a, sit morbi nunc sit id massa. Metus, scelerisque volutpat nec sit vel donec. Sagittis, id volutpat erat vel.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Can I cancel at anytime?</button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                      <div class="accordion-body">Cras vitae ac nunc orci. Purus amet tortor non at phasellus ultricies hendrerit. Eget a, sit morbi nunc sit id massa. Metus, scelerisque volutpat nec sit vel donec. Sagittis, id volutpat erat vel.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">How do I get a receipt for my purchase?</button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                      <div class="accordion-body">Cras vitae ac nunc orci. Purus amet tortor non at phasellus ultricies hendrerit. Eget a, sit morbi nunc sit id massa. Metus, scelerisque volutpat nec sit vel donec. Sagittis, id volutpat erat vel.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">Which license do I need?</button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                      <div class="accordion-body">Cras vitae ac nunc orci. Purus amet tortor non at phasellus ultricies hendrerit. Eget a, sit morbi nunc sit id massa. Metus, scelerisque volutpat nec sit vel donec. Sagittis, id volutpat erat vel.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">How do I get access to a theme I purchased?</button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
                      <div class="accordion-body">Cras vitae ac nunc orci. Purus amet tortor non at phasellus ultricies hendrerit. Eget a, sit morbi nunc sit id massa. Metus, scelerisque volutpat nec sit vel donec. Sagittis, id volutpat erat vel.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> --}}

@endsection
