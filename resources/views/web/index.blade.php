@extends('web.layouts.app')
@section('title', 'Home Page')

@section('content')

    <!-- talent by category -->
    <section class="pb-0  pb100-xs">
        <div class="container">
          <div class="row align-items-center wow fadeInUp" data-wow-delay="300ms">
            <div class="col-lg-9">
              <div class="main-title2">
                <h2 class="title">Browse talent by category</h2>
                <p class="paragraph">Aliquam lacinia diam quis lacus euismod</p>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="text-start text-lg-end mb-4">
                <a class="ud-btn btn-light-thm bdrs90" href="{{ route('web.services') }}">All Category<i class="fal fa-arrow-right-long"></i></a>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 wow fadeInUp" data-wow-delay="300ms">
              <div class="dots_none slider-dib-sm slider-5-grid vam_nav_style owl-theme owl-carousel">
                @foreach($subcategories as $index => $subcategory)
                  <div class="item">
                    <a href="{{ route('web.services', [$subcategory->category_id, $subcategory->id]) }}" class="text-decoration-none">
                      <div class="feature-style1 mb30 bdrs16">
                        <div class="feature-img bdrs16 overflow-hidden">
                          @if($subcategory->image)
                            <img class="w-100" src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name }}">
                          @else
                            <img class="w-100" src="{{ asset($staticImages[$index % count($staticImages)]) }}" alt="{{ $subcategory->name }}">
                          @endif
                        </div>
                        <div class="feature-content">
                          <div class="top-area">
                            <h6 class="title mb-1">{{ $subcategory->gigs_count }} {{ $subcategory->gigs_count == 1 ? 'skill' : 'skills' }}</h6>
                            <h5 class="text">{{ $subcategory->name }}</h5>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Popular Services -->
    <section class="pt60 pb100">
        <div class="container">
          <div class="row align-items-center wow fadeInUp">
            <div class="col-xl-3">
              <div class="main-title mb30-lg">
                <h2 class="title">Popular Services</h2>
                <p class="paragraph">Most viewed and all-time top-selling services</p>
              </div>
            </div>
            <div class="col-xl-9">
              <div class="navpill-style2 at-home9 mb50-lg">
                <ul class="nav nav-pills mb20 justify-content-xl-end" id="pills-tab" role="tablist">
                    @foreach($categories as $index => $category)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw500 dark-color {{ $index == 0 ? 'active' : '' }}"
                                id="pills-{{ $category->id }}-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-{{ $category->id }}"
                                type="button"
                                role="tab"
                                aria-controls="pills-{{ $category->id }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="navpill-style2">
                <div class="tab-content ha" id="pills-tabContent">
                    @foreach($categories as $index => $category)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="pills-{{ $category->id }}" role="tabpanel" aria-labelledby="pills-{{ $category->id }}-tab">
                            <div class="row">
                                @foreach($category->gigs->take(8) as $gig)
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="listing-style1 bdrs16">
                                            <div class="list-thumb">
                                                <img class="w-100" src="{{ asset($gig->thumbnail ?? 'storage/' . $gig->images->first()->image_path ?? 'web/images/listings/g-1.jpg') }}" alt="{{ $gig->title }}">
                                                {{-- <a href="#" class="listing-fav fz12"><span class="far fa-heart"></span></a> --}}
                                            </div>
                                            <div class="list-content">
                                                <p class="list-text body-color fz14 mb-1">{{ $gig->subcategory->name ?? 'Subcategory' }}</p>
                                                <h5 class="list-title">
                                                    <a href="{{ route('gigs.show', $gig->slug) }}">{{ Str::limit($gig->title, 50) }}</a>
                                                </h5>
                                                {{-- <div class="review-meta d-flex align-items-center">
                                                    <i class="fas fa-star fz10 review-color me-2"></i>
                                                    <p class="mb-0 body-color fz14">
                                                        <span class="dark-color me-2">{{ $gig->rating ?? '0.0' }}</span>
                                                        {{ $gig->ratings_count }} reviews
                                                    </p>
                                                </div> --}}
                                                <hr class="my-2">
                                                <div class="list-meta d-flex justify-content-between align-items-center mt15">
                                                    <a class="d-flex" href="#">
                                                        <span class="position-relative mr10">
                                                            <img class="rounded-circle wa" src="{{ asset($gig->user->profile->photo ?? 'web/images/team/fl-s-2.png') }}" alt="Freelancer Photo">
                                                            <span class="online-badges"></span>
                                                        </span>
                                                        <div>
                                                          <div>
                                                            <span class="fz14 notranslate" translate="no">{{ $gig->user->name }}</span>
                                                          </div>
                                                          <div class="budget">
                                                              <p class="mb-0 body-color">Starting at<span class="fz17 fw500 dark-color ms-1">€{{ $gig->starting_price }}</span></p>
                                                          </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-lg-12">
                                    <div class="text-center mt30">
                                        {{-- <a class="ud-btn btn-light-thm bdrs60" href="{{ route('category.show', $category->slug) }}">
                                            All Services<i class="fal fa-arrow-right-long"></i>
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Home Banner Style V1 -->
    <section class="hero-home2 pb100-xs">
        <div class="container">
          <div class="row mb60 mb0-xl">
            <div class="col-xl-7">
              <div class="pr30 pr0-lg mb30-md position-relative">
                <h1 class="animate-up-1 mb25 text-white">Find the service <br class="d-none d-xl-block">
                    that best suits you</h1>
                <p class="text-white animate-up-2">search for the professional based on your needs</p>
                <div class="advance-search-tab  bdrs4-sm bdrs60 banner-btn position-relative zi1 animate-up-3 mt30 pb30">
                    {{-- <form class="form-search position-relative" action="{{ route('services.search') }}" method="GET" >
                        <div class="row justify-content-between">
                            <div class="col-md-8 col-lg-8 col-xl-6">
                                <div class="advance-search-field mb10-sm">
                                    <div class="box-search">
                                        <span class="icon far fa-magnifying-glass"></span>
                                        <input class="form-control" type="text" name="search" placeholder="Search for service or explore relevant subcategories.">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4 col-xl-3">
                                <div class="bselect-style1 bdrl1 bdrn-sm">
                                    <select class="selectpicker" name="category_id" data-width="100%">
                                        <option>Choose Category</option>
                                        @foreach($active_categories as $avcat)
                                            <option data-tokens="Graphics&Design" value="{{ $avcat->id }}">{{ $avcat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-2 col-xl-3">
                                <div class="text-center text-xl-start">
                                    <button type="submit" class="ud-btn btn-thm w-100 bdrs60" type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </form> --}}
                </div>
                <div class="row mt20 animate-up-4">
                  <div class="col-xl-9">
                    <div class="row justify-content-between">
                      <div class="col-6 col-sm-3 funfact_one at-home2-hero">
                        <div class="details">
                          <ul class="ps-0 mb-0 d-flex">
                            <li><div class="timer">68</div></li>
                            <li><span>K</span></li>
                          </ul>
                          <p class="text-white mb-0">Total Freelancer</p>
                        </div>
                      </div>
                      <div class="col-6 col-sm-3 funfact_one at-home2-hero">
                        <div class="details">
                          <ul class="ps-0 mb-0 d-flex">
                            <li><div class="timer">74</div></li>
                            <li><span>K</span></li>
                          </ul>
                          <p class="text-white mb-0">Positive Review</p>
                        </div>
                      </div>
                      <div class="col-6 col-sm-3 funfact_one at-home2-hero">
                        <div class="details">
                          <ul class="ps-0 mb-0 d-flex">
                            <li><div class="timer">40</div></li>
                            <li><span>K</span></li>
                          </ul>
                          <p class="text-white mb-0">Order recieved</p>
                        </div>
                      </div>
                      <div class="col-6 col-sm-3 funfact_one at-home2-hero pe-0">
                        <div class="details">
                          <ul class="ps-0 mb-0 d-flex">
                            <li><div class="timer">36</div></li>
                            <li><span>K</span></li>
                          </ul>
                          <p class="text-white mb-0">Projects Completed</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-5 d-none d-xl-block position-relative">
              <img src="{{ asset('web/images/about/about-1.png') }}" alt="" class="animate-up-1 main-img-home2">
              <div class="home2-hero-content position-relative">
                <div class="iconbox-small1 d-none d-xl-flex wow fadeInRight default-box-shadow4 bounce-x animate-up-1">
                  <span class="icon flaticon-review"></span>
                  <div class="details pl20">
                    <h6 class="mb-1">Proof of quality</h6>
                    <p class="text fz13 mb-0">Lorem Ipsum Dolar Amet</p>
                  </div>
                </div>
                <div class="iconbox-small2 d-none d-xl-flex wow fadeInLeft default-box-shadow4 bounce-y animate-up-2">
                  <span class="icon flaticon-review"></span>
                  <div class="details pl20">
                    <h6 class="mb-1">Safe and secure</h6>
                    <p class="text fz13 mb-0">Lorem Ipsum Dolar Amet</p>
                  </div>
                </div>
                <img src="{{ asset('web/images/about/happy-client.png') }}" alt="" class="bounce-x bdrs16 img-1 default-box-shadow4">
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Need something -->
    <section class="our-features pb90 pb30-md pt60">
        <div class="container wow fadeInUp">
          <div class="row">
            <div class="col-lg-12">
              <div class="main-title">
                <h2>Need something done?</h2>
                <p class="text">Discover our most popular and top-rated service categories.</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-4">
              <div class="iconbox-style1 at-home5 p-0">
                <div class="icon before-none"><span class="flaticon-cv"></span></div>
                <div class="details">
                  <h4 class="title mt10 mb-3">Post your services</h4>
                  <p class="text">Make your service offerings known to the public.</p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="iconbox-style1 at-home5 p-0">
                <div class="icon before-none"><span class="flaticon-web-design"></span></div>
                <div class="details">
                  <h4 class="title mt10 mb-3">Private Chat</h4>
                  <p class="text">Interact directly with centers and professionals and evaluate the service that is right for you.</p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="iconbox-style1 at-home5 p-0">
                <div class="icon before-none"><span class="flaticon-secure"></span></div>
                <div class="details">
                  <h4 class="title mt10 mb-3">Choose the professional</h4>
                  <p class="text">Look for the service that best suits your needs.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- CTA Banner -->
    {{-- <section class="cta-banner-about2 before-none at-home2 position-relative py-0">
        <div class="container position-relative">
          <div class="row align-items-center">
            <div class="col-lg-7 col-xl-5 mb100-md">
              <div class="mb30">
                <h5 class="text-thm">For clients</h5>
                <h2 class="title">Find talent your way</h2>
              </div>
              <p class="text">Work with the largest network of independent professionals and <br class="d-none d-lg-block"> get things done—from quick turnarounds to big transformations.</p>
              <a class="ud-btn btn-thm bdrs90 default-box-shadow2 mt15 mb30-sm" href="{{ route('web.contact') }}">Contact Us<i class="fal fa-arrow-right-long"></i></a>
            </div>
            <div class="col-lg-5 col-xl-4 offset-xl-1 position-relative">
              <div class="listbox-style1 px30 py-5 bdrs16 bgc-thm2 mt30-md position-relative">
                <div class="list-style1">
                  <ul class="mb-0">
                    <li class="text-white fw500"><i class="far fa-check dark-color bgc-white"></i>The best for every budget</li>
                    <li class="text-white fw500"><i class="far fa-check dark-color bgc-white"></i>Quality work done quickly</li>
                    <li class="text-white fw500"><i class="far fa-check dark-color bgc-white"></i>Protected payments, every time</li>
                    <li class="text-white fw500 mb-0"><i class="far fa-check dark-color bgc-white"></i>24/7 support</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <img class="home2-cta-img" src="{{ asset('web/images/about/about-10.jpg') }}" alt="">
    </section> --}}
    <!-- Our Partners -->
    {{-- <section class="our-partners">
        <div class="container">
          <div class="row wow fadeInUp">
            <div class="col-lg-12">
              <div class="main-title text-center">
                <h6>Trusted by the world’s best</h6>
              </div>
            </div>
          </div>
          <div class="row wow fadeInUp">
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/1.png') }}" alt="1.png') }}"></div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/2.png') }}" alt="2.png') }}"></div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/3.png') }}" alt="3.png') }}"></div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/4.png') }}" alt="4.png') }}"></div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/5.png') }}" alt="5.png') }}"></div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <div class="partner_item text-center mb30-lg"><img class="wa m-auto" src="{{ asset('web/images/partners/6.png') }}" alt="6.png') }}"></div>
            </div>
          </div>
        </div>
    </section> --}}
    <!-- Highest Rated Freelancers -->
    <section class="pt90 pt60-md pb130 pb60-md d-none">
        <div class="container">
          <div class="row align-items-center wow fadeInUp">
            <div class="col-lg-9">
              <div class="main-title">
                <h2 class="title">Highest Rated Freelancers</h2>
                <p class="paragraph">Lorem ipsum dolor sit amet, consectetur.</p>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="text-start text-lg-end mb-4">
                <a class="ud-btn btn-light-thm bdrs90" href="#">All Freelancers<i class="fal fa-arrow-right-long"></i></a>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="navi_pagi_bottom_center slider-4-grid owl-carousel owl-theme">
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-1.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Robert Fox</h5>
                      <p class="mb-0">Nursing Assistant</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-2.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Kristin Watson</h5>
                      <p class="mb-0">Dog Trainer</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-3.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Darrell Steward</h5>
                      <p class="mb-0">Medical Assistant</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-4.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Theresa Webb</h5>
                      <p class="mb-0">Marketing Coordinator</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-1.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Robert Fox</h5>
                      <p class="mb-0">Nursing Assistant</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-2.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Kristin Watson</h5>
                      <p class="mb-0">Dog Trainer</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="freelancer-style1 text-center bdr1 hover-box-shadow mb60 bdrs16">
                    <div class="thumb w90 mb25 mx-auto position-relative rounded-circle">
                      <img class="rounded-circle mx-auto" src="{{ asset('web/images/team/fl-3.png') }}" alt="">
                      <span class="online"></span>
                    </div>
                    <div class="details">
                      <h5 class="title mb-1">Darrell Steward</h5>
                      <p class="mb-0">Medical Assistant</p>
                      <div class="review"><p><i class="fas fa-star fz10 review-color pr10"></i><span class="dark-color">4.9</span> (595 reviews)</p></div>
                      <div class="skill-tags d-flex align-items-center justify-content-center mb5">
                        <span class="tag">Figma</span>
                        <span class="tag mx10">Sketch</span>
                        <span class="tag">HTML5</span>
                      </div>
                      <hr class="opacity-100 mt20 mb15">
                      <div class="fl-meta d-flex align-items-center justify-content-between">
                        <a class="meta fw500 text-start">Location<br><span class="fz14 fw400">London</span></a>
                        <a class="meta fw500 text-start">Rate<br><span class="fz14 fw400">$90 / hr</span></a>
                        <a class="meta fw500 text-start">Job Success<br><span class="fz14 fw400">%98</span></a>
                      </div>
                      <div class="d-grid mt15">
                        <a href="#" class="ud-btn btn-light-thm bdrs90">View Profile<i class="fal fa-arrow-right-long"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Learn With FitScout -->
    <section class="bgc-thm3">
        <div class="container">
          <div class="row align-items-md-center">
            <div class="col-md-6 col-lg-8 mb30-md wow fadeInUp" data-wow-delay="100ms">
              <div class="main-title">
                <h2 class="title">People Love To Learn With FitScout</h2>
                <p class="paragraph">Lorem ipsum dolor sit amet, consectetur.</p>
              </div>
              <div class="row">
                <div class="col-sm-6 col-lg-4">
                  <div class="funfact_one">
                    <div class="details">
                      <ul class="ps-0 d-flex mb-0">
                        <li><div class="timer">4</div></li>
                        <li><div>.</div></li>
                        <li><div class="timer">9</div></li>
                        <li><span>/</span></li>
                        <li><div class="timer">5</div></li>
                      </ul>
                      <p class="text mb-0">Clients rate professionals on FitScout</p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="funfact_one">
                    <div class="details">
                      <ul class="ps-0 d-flex mb-0">
                        <li><div class="timer">99</div></li>
                        <li><span>%</span></li>
                      </ul>
                      <p class="text mb-0">95% of customers are satisfied <br> through to see their freelancers</p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="funfact_one">
                    <div class="details">
                      <h2>Award winner</h2>
                      <p class="text mb-0">Home ownership</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
              <div class="testimonial-slider2 mb15 navi_pagi_bottom_center slider-1-grid owl-carousel owl-theme wow fadeInUp" data-wow-delay="300ms">
                <div class="item">
                  <div class="testimonial-style1 default-box-shadow1 position-relative bdrs16 mb35">
                    <div class="testimonial-content">
                      <h4 class="title text-thm">Great Work</h4>
                      <span class="icon fas fa-quote-left"></span>
                      <h4 class="t_content">“I found the course material to be highly engaging, and the instructors to be helpful and communicative.”</h4>
                    </div>
                    <div class="thumb d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img class="wa" src="{{ asset('web/images/testimonials/testimonial-1.png') }}" alt="">
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Courtney Henry</h6>
                        <p class="fz14 mb-0">Web Designer</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="testimonial-style1 default-box-shadow1 position-relative bdrs16 mb35">
                    <div class="testimonial-content">
                      <h4 class="title text-thm">Great Work</h4>
                      <span class="icon fas fa-quote-left"></span>
                      <h4 class="t_content">“I found the course material to be highly engaging, and the instructors to be helpful and communicative.”</h4>
                    </div>
                    <div class="thumb d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img class="wa" src="{{ asset('web/images/testimonials/testimonial-2.png') }}" alt="">
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Courtney Henry</h6>
                        <p class="fz14 mb-0">Web Designer</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="testimonial-style1 default-box-shadow1 position-relative bdrs16 mb35">
                    <div class="testimonial-content">
                      <h4 class="title text-thm">Great Work</h4>
                      <span class="icon fas fa-quote-left"></span>
                      <h4 class="t_content">“I found the course material to be highly engaging, and the instructors to be helpful and communicative.”</h4>
                    </div>
                    <div class="thumb d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img class="wa" src="{{ asset('web/images/testimonials/testimonial-3.png') }}" alt="">
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Courtney Henry</h6>
                        <p class="fz14 mb-0">Web Designer</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="testimonial-style1 default-box-shadow1 position-relative bdrs16 mb35">
                    <div class="testimonial-content">
                      <h4 class="title text-thm">Great Work</h4>
                      <span class="icon fas fa-quote-left"></span>
                      <h4 class="t_content">“I found the course material to be highly engaging, and the instructors to be helpful and communicative.”</h4>
                    </div>
                    <div class="thumb d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img class="wa" src="{{ asset('web/images/testimonials/testimonial-3.png') }}" alt="">
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Courtney Henry</h6>
                        <p class="fz14 mb-0">Web Designer</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Pricing Table Area -->
    {{-- <section class="our-pricing pb90">
        <div class="container">
          <div class="row">
            <div class="col-lg-6 m-auto wow fadeInUp">
              <div class="main-title text-center mb30">
                <h2 class="title">Membership Plans</h2>
                <p class="paragraph mt10">Give your visitor a smooth online experience with a solid UX design</p>
              </div>
            </div>
          </div>
          <div class="row wow fadeInUp" data-wow-delay="200ms">
            <div class="col-lg-12">
              <div class="pricing_packages_top d-flex align-items-center justify-content-center mb60">
                <div class="toggle-btn">
                  <span class="pricing_save1 dark-color ff-heading">Billed Monthly</span>
                  <label class="switch">
                    <input type="checkbox" id="checbox" onclick="check()"/>
                    <span class="pricing_table_switch_slide round"></span>
                  </label>
                  <span class="pricing_save2 dark-color ff-heading">Billed Yearly</span>
                  <span class="pricing_save3">Save 20%</span>
                </div>
              </div>
            </div>
          </div>
          <div class="row wow fadeInUp" data-wow-delay="300ms">
            <div class="col-sm-6 col-xl-3">
              <div class="pricing_packages at-home2 text-center bdrs16">
                <div class="heading mb10">
                  <h1 class="text2">$29 <small>/ monthly</small></h1>
                  <h4 class="package_title mt-2">Basic Plan</h4>
                </div>
                <div class="details">
                  <p class="text mb30">One time fee for one listing or task highlighted in search results.</p>
                  <div class="pricing-list mb40">
                    <ul class="px-0">
                      <li>1 Listing</li>
                      <li>30 Days Visibility</li>
                      <li>Highlighted in Search Results</li>
                      <li>4 Revisions</li>
                      <li>9 days Delivery Time</li>
                      <li>Products Support</li>
                    </ul>
                  </div>
                  <div class="d-grid">
                    <a href="#" class="ud-btn btn-light-thm">Buy Now<i class="fal fa-arrow-right-long"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="pricing_packages at-home2 active text-center bdrs16">
                <div class="heading mb10">
                  <h1 class="text2">$49 <small>/ monthly</small></h1>
                  <h4 class="package_title mt-2">Standard Plan</h4>
                </div>
                <div class="details">
                  <p class="text mb30">One time fee for one listing or task highlighted in search results.</p>
                  <div class="pricing-list mb40">
                    <ul class="px-0">
                      <li>1 Listing</li>
                      <li>30 Days Visibility</li>
                      <li>Highlighted in Search Results</li>
                      <li>4 Revisions</li>
                      <li>9 days Delivery Time</li>
                      <li>Products Support</li>
                    </ul>
                  </div>
                  <div class="d-grid">
                    <a href="#" class="ud-btn btn-light-thm">Buy Now<i class="fal fa-arrow-right-long"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="pricing_packages at-home2 text-center bdrs16">
                <div class="heading mb10">
                  <h1 class="text2">$89 <small>/ monthly</small></h1>
                  <h4 class="package_title mt-2">Extended Plan</h4>
                </div>
                <div class="details">
                  <p class="text mb30">One time fee for one listing or task highlighted in search results.</p>
                  <div class="pricing-list mb40">
                    <ul class="px-0">
                      <li>1 Listing</li>
                      <li>30 Days Visibility</li>
                      <li>Highlighted in Search Results</li>
                      <li>4 Revisions</li>
                      <li>9 days Delivery Time</li>
                      <li>Products Support</li>
                    </ul>
                  </div>
                  <div class="d-grid">
                    <a href="#" class="ud-btn btn-light-thm">Buy Now<i class="fal fa-arrow-right-long"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="pricing_packages at-home2 text-center bdrs16">
                <div class="heading mb10">
                  <h1 class="text2">$129 <small>/ monthly</small></h1>
                  <h4 class="package_title mt-2">Enterprise Plan</h4>
                </div>
                <div class="details">
                  <p class="text mb30">One time fee for one listing or task highlighted in search results.</p>
                  <div class="pricing-list mb40">
                    <ul class="px-0">
                      <li>1 Listing</li>
                      <li>30 Days Visibility</li>
                      <li>Highlighted in Search Results</li>
                      <li>4 Revisions</li>
                      <li>9 days Delivery Time</li>
                      <li>Products Support</li>
                    </ul>
                  </div>
                  <div class="d-grid">
                    <a href="#" class="ud-btn btn-light-thm">Buy Now<i class="fal fa-arrow-right-long"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section> --}}

@endsection
