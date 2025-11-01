@extends('web.layouts.app')

@section('title', 'Home Page')

@section('content')
<div style="padding-top: 78px;"></div>
<!-- Breadcumb Sections -->
<section class="breadcumb-section">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-lg-10">
          <div class="breadcumb-style1 mb10-xs">
            <div class="breadcumb-list">
              <a href="{{ route('web.index') }}">Home</a>
              <a href="{{ route('web.services') }}">Services</a>
              <a href="#">{{ $gig->title }}</a>
            </div>
          </div>
        </div>
        <div class="col-sm-4 col-lg-2">
          <div class="d-flex align-items-center justify-content-sm-end">
            <div class="share-save-widget d-flex align-items-center">
              <span class="icon flaticon-share dark-color fz12 mr10"></span>
              <div class="h6 mb-0">Share</div>
            </div>
            <div class="share-save-widget d-flex align-items-center ml15">
              <span class="icon flaticon-like dark-color fz12 mr10"></span>
              <div class="h6 mb-0">Save</div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- Breadcumb Sections -->
<section class="breadcumb-section pt-0">
    <div class="cta-service-single cta-banner mx-auto maxw1700 pt120 pt60-sm pb120 pb60-sm bdrs16 position-relative overflow-hidden d-flex align-items-center mx20-lg px30-lg">
      <img class="left-top-img wow zoomIn" src="images/vector-img/left-top.png" alt="">
      <img class="right-bottom-img wow zoomIn" src="images/vector-img/right-bottom.png" alt="">
      <img class="service-v1-vector bounce-y d-none d-xl-block" src="images/vector-img/vector-service-v1.png" alt="">
      <div class="container">
        <div class="row wow fadeInUp">
          <div class="col-xl-7">
            <div class="position-relative">
                <h2>{{ $gig->title }}</h2>
                <span class="fz14 notranslate" translate="no">{{ $gig->user->name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<!-- Service Details -->
<section class="pt10 pb90 pb30-md">
    <div class="container">
      <div class="row wrap">
        <div class="col-lg-8">
          <div class="column">
            <div class="row">
              <div class="col-sm-6 col-md-4">
                <div class="iconbox-style1 contact-style d-flex align-items-start mb30">
                  <div class="icon flex-shrink-0"><span class="flaticon-calendar"></span></div>
                  <div class="details">
                    <h5 class="title">Delivery Time</h5>
                    <p class="mb-0 text">{{ $gig->delivery_time }} Days</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="iconbox-style1 contact-style d-flex align-items-start mb30">
                  <div class="icon flex-shrink-0"><span class="flaticon-goal"></span></div>
                  <div class="details">
                    <h5 class="title">Language</h5>
                    <p class="mb-0 text">{{ $gig->user->profile->languages }}</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="iconbox-style1 contact-style d-flex align-items-start mb30">
                  <div class="icon flex-shrink-0"><span class="flaticon-tracking"></span></div>
                  <div class="details">
                    <h5 class="title">Location</h5>
                    <p class="mb-0 text">{{ $gig->user->profile->city }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="service-single-sldier vam_nav_style slider-1-grid owl-carousel owl-theme mb60">

                @foreach($gig->images as $image)
                    <div class="item">
                        <div class="thumb p50 p30-sm">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-100" alt="">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="service-about">
              <h4>About</h4>
              <p class="text mb30">{!! strip_tags($gig->about) !!}</p>
              <hr class="opacity-100 mb60">
            </div>
          </div>
        </div>
        <div class="col-lg-4">
            @php $packages = $gig->packages @endphp
            @if (count($packages) > 0)
            <div class="column">
                <div class="blog-sidebar ms-lg-auto">
                    <div class="price-widget">
                        <div class="navtab-style1">
                            <nav>
                                <div class="nav nav-tabs mb20" id="nav-tab2p" role="tablist">
                                    @foreach($packages as $key => $package)
                                        <button class="nav-link fw500 {{ $key == 0 ? 'active' : '' }}" id="nav-item{{ $key }}p-tab" data-bs-toggle="tab" data-bs-target="#nav-item{{ $key }}p" type="button" role="tab" aria-controls="nav-item{{ $key }}p" aria-selected="{{ $key == 0 ? 'true' : 'false' }}">{{ $package->title }}</button>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                @foreach($packages as $key => $package)
                                    <div class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}" id="nav-item{{ $key }}p">
                                        <div class="price-content">
                                            <div class="price">€{{ $package->price }}</div>
                                            <div class="h5 mb-2">{{ $package->title }}</div>
                                            <p class="text fz14">{!! strip_tags($package->description) !!}</p>
                                            <ul class="p-0 mb15 d-sm-flex align-items-center">
                                                <li class="fz14 fw500 dark-color">
                                                    <i class="flaticon-sandclock fz20 text-thm2 me-2 vam"></i>{{ $package->delivery_time }} Days Delivery
                                                </li>
                                                @if($package->revision_limit > 0)
                                                  <li class="fz14 fw500 dark-color ml20 ml0-xs">
                                                      <i class="flaticon-recycle fz20 text-thm2 me-2 vam"></i>{{ $package->revision_limit }} Revisions
                                                  </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="freelancer-style1 service-single mb-0">
                        <div class="wrapper d-flex align-items-center">
                            <div class="thumb position-relative mb25">
                                <img class="rounded-circle mx-auto" src="{{ asset($gig->user->profile->photo ?? 'web/images/team/fl-s-2.png') }}" alt="">
                                <span class="online"></span>
                            </div>
                            <div class="ml20 notranslate" translate="no">
                                <h5 class="title mb-1">{{ $gig->user->name }}</h5>
                                <p class="mb-0">{{ $gig->user->name }}</p>
                            </div>
                        </div>
                        <hr class="opacity-100">
                        <div class="details">
                            <div class="fl-meta d-flex align-items-center justify-content-between">
                                <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">{{ $gig->user->profile->city }}</span></a>
                                <a class="meta fw500 text-start">Langauage<br><span class="fz14 fw400">{{ $gig->user->profile->languages }}</span></a>
                            </div>
                        </div>
                        <div class="d-grid mt30">
                            @guest
                                <a href="{{ route('web.login') }}" class="ud-btn btn-thm-border">Contact Me<i class="fal fa-arrow-right-long"></i></a>
                            @endguest
                            @auth
                                <a href="{{ route('gig.contact', $gig->id) }}" class="ud-btn btn-thm-border">Contact Me<i class="fal fa-arrow-right-long"></i></a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
      </div>
    </div>
</section>

<!-- Related Gigs -->
@if($relatedGigs->count() > 0)
<section class="pt30 pb90 pb30-md">
    <div class="container">
      <div class="row wow fadeInUp">
        <div class="col-lg-12">
          <div class="main-title mb35">
            <h2>Related Services</h2>
            <p class="text">Discover similar services from other professionals</p>
          </div>
        </div>
      </div>
      <div class="row wow fadeInUp">
        @foreach($relatedGigs as $relatedGig)
        <div class="col-sm-6 col-lg-3">
          <div class="listing-style1">
            <div class="list-thumb">
              @if($relatedGig->images->count() > 0)
                <img class="w-100" src="{{ asset($relatedGig->images->first()->image_path) }}" alt="{{ $relatedGig->title }}">
              @else
                <img class="w-100" src="{{ asset('web/images/listings/default-gig.jpg') }}" alt="{{ $relatedGig->title }}">
              @endif
              <a href="#" class="listing-fav fz12"><span class="far fa-heart"></span></a>
            </div>
            <div class="list-content">
              <p class="list-text body-color fz14 mb-1">{{ $relatedGig->subcategory->name ?? 'Service' }}</p>
              <h5 class="list-title"><a href="{{ route('gigs.show', $relatedGig->slug) }}">{{ Str::limit($relatedGig->title, 60) }}</a></h5>
              <div class="review-meta d-flex align-items-center">
                <i class="fas fa-star fz10 review-color me-2"></i>
                <p class="mb-0 body-color fz14">
                  <span class="dark-color me-2">{{ number_format($relatedGig->rating, 2) }}</span>
                  {{ $relatedGig->ratings_count }} {{ $relatedGig->ratings_count == 1 ? 'review' : 'reviews' }}
                </p>
              </div>
              <hr class="my-2">
              <div class="list-meta d-flex justify-content-between align-items-center mt15">
                <a href="{{ route('gigs.show', $relatedGig->slug) }}">
                  <span class="position-relative mr10">
                    <img class="rounded-circle" src="{{ asset($relatedGig->user->profile->photo ?? 'web/images/team/default-avatar.png') }}" alt="{{ $relatedGig->user->name }}">
                    <span class="online-badge"></span>
                  </span>
                  <span class="fz14 notranslate" translate="no">{{ $relatedGig->user->name }}</span>
                </a>
                <div class="budget">
                  <p class="mb-0 body-color">Starting at<span class="fz17 fw500 dark-color ms-1">${{ number_format($relatedGig->starting_price, 0) }}</span></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
</section>
@endif

@endsection
