<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Application')</title>

    <!-- Include CSS files -->
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/ace-responsive-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/slider.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/ud-custom-spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/responsive.css') }}">

    <!-- Favicon -->
    <link href="{{ asset('web/images/favicon.png') }}" sizes="128x128" rel="shortcut icon" type="image/x-icon" />
    <link href="{{ asset('web/images/favicon.png') }}" sizes="128x128" rel="shortcut icon" />
    <link href="{{ asset('web/images/apple-touch-icon-60x60.png') }}" sizes="60x60" rel="apple-touch-icon">
    <link href="{{ asset('web/images/apple-touch-icon-72x72.png') }}" sizes="72x72" rel="apple-touch-icon">
    <link href="{{ asset('web/images/apple-touch-icon-114x114.png') }}" sizes="114x114" rel="apple-touch-icon">
    <link href="{{ asset('web/images/apple-touch-icon-180x180.png') }}" sizes="180x180" rel="apple-touch-icon">
    @stack('styles')
</head>
<body>

    <!-- Toolbar Component (for authenticated users) -->
    @auth
        @livewire('toolbar')
    @endauth

    <div class="wrapper ovh">
        <div class="preloader"></div>
        <!-- Main Header Nav -->
        <header class="header-nav nav-homepage-style stricky main-menu">
           <!-- Ace Responsive Menu -->
           <nav class="posr">
              <div class="container-fluid posr menu_bdrt1 px30">
                 <div class="row align-items-center justify-content-between">
                    <div class="col-auto px-0">
                       <div class="d-flex align-items-center justify-content-between">
                          <div class="logos br-white-light pr30 pr5-xl">
                             <a class="header-logo logo1" href="{{ route('web.index') }}"><img src="{{ asset('web/images/logo.png') }}" alt="Header Logo" width="120px"></a>
                             <a class="header-logo logo2" href="{{ route('web.index') }}"><img src="{{ asset('web/images/logo-dark.png') }}" alt="Header Logo" width="120px"></a>
                          </div>
                          <div class="home1_style d-flex align-items-center">
                            <span class="btn-mega fw500" data-bs-toggle="modal" href="#exampleModalToggle" role="button">
                                <span class="pl30 pl10-xl pr5 fz15 vam flaticon-search"></span> Search
                            </span>
                             <div id="mega-menu">
                                <a class="btn-mega fw500" href="#"><span class="pl30 pl10-xl pr5 fz15 vam flaticon-menu"></span> Categories</a>
                                <ul class="menu ps-0">
                                    @foreach($active_categories as $category)
                                        <li>
                                            <a class="dropdown" href="#">
                                                {{-- Removed icon --}}
                                                <span class="menu-title">{{ $category->name }}</span>
                                            </a>

                                            @if($category->subcategories->isNotEmpty())
                                                <div class="drop-menu d-flex justify-content-between flex-wrap">

                                                {{-- Show open category name at the top --}}
                                                <div class="one-third">
                                                    <div class="h6 cat-title">{{ $category->name }}</div>
                                                        <ul class="ps-0 mb40">
                                                            @foreach($category->subcategories->take(6) as $subcategory)
                                                                <li>
                                                                    <a href="{{ route('web.services', [$category->id, $subcategory->id]) }}">
                                                                        {{ $subcategory->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    {{-- If more subcategories, split across columns --}}
                                                    @foreach($category->subcategories->slice(6)->chunk(6) as $chunk)
                                                        <div class="one-third">
                                                            <ul class="ps-0 mb40">
                                                                @foreach($chunk as $subcategory)
                                                                    <li>
                                                                        <a href="{{ route('web.services', [$category->id, $subcategory->id]) }}">
                                                                        {{ $subcategory->name }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach

                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>

                             </div>
                          </div>
                       </div>
                    </div>
                    <div class="col-auto px-0">
                       <div class="d-flex align-items-center">
                            <!-- Responsive Menu Structure-->
                            <ul id="respMenu" class="ace-responsive-menu" data-menu-style="horizontal">
                                <li class="visible_list">
                                    <a class="list-item" href="{{ route('web.index') }}"><span class="title">Home</span></a>
                                </li>
                                <li class="visible_list">
                                    <a class="list-item" href="{{ route('web.about') }}"><span class="title">About Us</span></a>
                                </li>
                                <li class="visible_list">
                                    <a class="list-item" href="{{ route('web.services') }}"><span class="title">Services</span></a>
                                </li>
                                <li class="visible_list">
                                    <a class="list-item" href="{{ route('web.contact') }}"><span class="title">Contact Us</span></a>
                                </li>
                            </ul>
                            {{-- <a class="login-info bdrl1 pl15-lg pl30" data-bs-toggle="modal" href="#exampleModalToggle" role="button"><span class="flaticon-loupe"></span></a> --}}
                            <a class="login-info d-inline-flex mr15-lg mr30" href="#">
                                <div class="switch-toggle">
                                    <input id="language-toggle" class="check-toggle check-toggle-round-flat" type="checkbox">
                                    <label for="language-toggle"></label>
                                    <span class="on">English</span>
                                    <span class="off">Italian</span>
                                </div>
                            </a>
                            @guest
                                <a class="ud-btn btn-white add-joining me-2" href="{{ route('web.register') }}">Subscribe</a>
                                <a class="ud-btn btn-white add-joining" href="{{ route('web.login') }}">Log In</a>
                            @else
                                <form method="POST" action="{{ route('web.logout') }}">
                                    @csrf
                                    <button type="submit" class="ud-btn btn-white add-joining">Logout</button>
                                </form>
                            @endguest
                       </div>
                    </div>
                 </div>
              </div>
           </nav>
        </header>
        <!-- Search Modal -->
        <div class="search-modal">
           <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
              <div class="modal-dialog modal-lg">
                 <div class="modal-content">
                    <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalToggleLabel"></h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="advance-search-tab bgc-white p10 bdrs4-sm bdrs60 banner-btn position-relative zi1 animate-up-3">
                                <form class="form-search position-relative" action="{{ route('services.search') }}" method="GET" >
                                    <div class="row justify-content-between">
                                        <div class="col-md-8 col-lg-8 col-xl-6">
                                            <div class="advance-search-field mb10-sm">
                                                <div class="box-search">
                                                    <span class="icon far fa-magnifying-glass"></span>
                                                    <input class="form-control" type="text" name="search" placeholder="Search for service or explore relevant subcategories.">
                                                    {{-- <div class="search-suggestions">
                                                        <h6 class="fz14 ml30 mt25 mb-3">Popular Search</h6>
                                                        <div class="box-suggestions">
                                                        <ul class="px-0 m-0 pb-4">
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile app development</div>
                                                            </div>
                                                            </li>
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile app builder</div>
                                                            </div>
                                                            </li>
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile legends</div>
                                                            </div>
                                                            </li>
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile app ui ux design</div>
                                                            </div>
                                                            </li>
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile game app development</div>
                                                            </div>
                                                            </li>
                                                            <li>
                                                            <div class="info-product">
                                                                <div class="item_title">mobile app design</div>
                                                            </div>
                                                            </li>
                                                        </ul>
                                                        </div>
                                                    </div> --}}
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
                                </form>
                            </div>
                        </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
        <div class="hiddenbar-body-ovelay"></div>
        <!-- Mobile Nav  -->
        <div id="page" class="mobilie_header_nav stylehome1">
           <div class="mobile-menu">
              <div class="header bb-white-light">
                 <div class="menu_and_widgets">
                    <div class="mobile_menu_bar d-flex justify-content-between align-items-center">
                       <a class="mobile_logo" href="#"><img src="{{ asset('web/images/logo.png') }}" alt="" width="120px"></a>
                       <div class="right-side text-end">
                          @guest
                            <a class="text-white" href="{{ route('web.login') }}">Log In</a>
                          @else
                            <form method="POST" action="{{ route('web.logout') }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="text-white" style="background:none;border:none;padding:0;">Logout</button>
                            </form>
                          @endguest
                          <a class="menubar ml30" href="#menu"><img src="{{ asset('web/images/white-nav-icon.svg') }}" alt=""></a>
                       </div>
                    </div>
                 </div>
                 <div class="posr">
                    <div class="mobile_menu_close_btn"><span class="far fa-times"></span></div>
                 </div>
              </div>
           </div>
            <!-- /.mobile-menu -->
            <nav id="menu" class="">
                <ul>
                    <li>
                        <a href="{{ route('web.index') }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('web.about') }}">About Us</a>
                    </li>
                    <li>
                        <a href="{{ route('web.services') }}">Services</a>
                    </li>
                    <li>
                        <a href="{{ route('web.contact') }}">Contact Us</a>
                    </li>
                    <li>
                        <a href="{{ route('web.register') }}">Subscribe</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="body_content">
           @yield('content')  <!-- Page-specific content will be injected here -->
           <!-- Footer -->
           <section class="footer-style1 pt25 pb-0">
              <div class="container">
                 <div class="row bb-white-light pb10 mb60">
                    <div class="col-md-7">
                       <div class="d-block text-center text-md-start justify-content-center justify-content-md-start d-md-flex align-items-center mb-3 mb-md-0">
                          <a class="fz17 fw500 text-white mr15-md mr30" href="{{ route('web.term_of_services') }}">Terms of Service</a>
                          <a class="fz17 fw500 text-white mr15-md mr30" href="{{ route('web.privacy_policy') }}">Privacy Policy</a>
                          {{-- <a class="fz17 fw500 text-white" href="#">Site Map</a> --}}
                       </div>
                    </div>
                    <div class="col-md-5">
                       <div class="social-widget text-center text-md-end">
                          <div class="social-style1">
                             <a class="text-white me-2 fw500 fz17" href="#">Follow us</a>
                             <a href="#"><i class="fab fa-tiktok list-inline-item"></i></a>
                             <a href="#"><i class="fab fa-twitter list-inline-item"></i></a>
                             <a href="#"><i class="fab fa-instagram list-inline-item"></i></a>
                             {{-- <a href="#"><i class="fab fa-linkedin-in list-inline-item"></i></a> --}}
                          </div>
                       </div>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        <div class="link-style1 mb-4 mb-sm-5">
                            <h5 class="text-white mb15">Quick Link</h5>
                            <div class="link-list">
                                <a href="{{ route('web.about') }}">About Us</a>
                                <a href="{{ route('web.login') }}">Login</a>
                                <a href="{{ route('web.register') }}">Subscribe</a>
                                <a href="{{ route('web.services') }}">Services</a>
                                <a href="{{ route('web.contact') }}">Contact Us</a>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-sm-6 col-lg-3">
                       <div class="link-style1 mb-4 mb-sm-5">
                          <h5 class="text-white mb15">Categories</h5>
                          <ul class="ps-0">
                             <li><a href="#">Graphics & Design</a></li>
                             <li><a href="#">Digital Marketing</a></li>
                             <li><a href="#">Writing & Translation</a></li>
                             <li><a href="#">Video & Animation</a></li>
                             <li><a href="#">Music & Audio</a></li>
                             <li><a href="#">Programming & Tech</a></li>
                             <li><a href="#">Data</a></li>
                             <li><a href="#">Business</a></li>
                             <li><a href="#">Lifestyle</a></li>
                          </ul>
                       </div>
                    </div> --}}
                    {{-- <div class="col-sm-6 col-lg-4">
                       <div class="link-style1 mb-4 mb-sm-5">
                          <h5 class="text-white mb15">Support</h5>
                          <ul class="ps-0">
                            <li><a href="{{ route('web.term_of_services') }}">Privacy Policy</a></li>
                            <li><a href="{{ route('web.privacy_policy') }}">Terms of Service</a></li>
                          </ul>
                       </div>
                    </div> --}}
                    {{-- <div class="col-sm-6 col-lg-4">
                       <div class="footer-widget">
                            <div class="footer-widget mb-4 mb-sm-5">
                                <div class="mailchimp-widget">
                                    <h5 class="title text-white mb20">Subscribe</h5>
                                    <div class="mailchimp-style1">
                                    <input type="email" class="form-control" placeholder="Your email address">
                                    <button type="submit">Send</button>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </div> --}}
                 </div>
              </div>
              <div class="container white-bdrt1 py-4">
                 <div class="row align-items-center">
                    <div class="col-md-6">
                       <div class="text-center text-lg-start">
                          <p class="copyright-text mb-2 mb-md-0 text-white-light ff-heading">© FitScout. 2025. All rights reserved.</p>
                       </div>
                    </div>
                 </div>
              </div>
           </section>
           <a class="scrollToHome" href="#"><i class="fas fa-angle-up"></i></a>
        </div>
    </div>

    <!-- Include JS files -->
    <script src="{{ asset('web/js/jquery-3.6.4.min.js') }}"></script>
    <script src="{{ asset('web/js/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('web/js/popper.min.js') }}"></script>
    <script src="{{ asset('web/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('web/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('web/js/jquery.mmenu.all.js') }}"></script>
    <script src="{{ asset('web/js/ace-responsive-menu.js') }}"></script>
    <script src="{{ asset('web/js/jquery-scrolltofixed-min.js') }}"></script>
    <script src="{{ asset('web/js/wow.min.js') }}"></script>
    <script src="{{ asset('web/js/owl.js') }}"></script>
    <script src="{{ asset('web/js/parallax.js') }}"></script>
    <script src="{{ asset('web/js/script.js') }}"></script>

    {{-- google translator --}}
    <style>
        .skiptranslate {
            display: none !important;
        }
        body {
            top: 0px !important;
        }
        .switch-toggle {
            position: relative;
            display: inline-block;
            margin: 0 5px;
        }

        .switch-toggle > span {
            position: absolute;
            top: 5px;
            pointer-events: none;
            font-family: 'Helvetica', Arial, sans-serif;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            text-shadow: 0 1px 0 rgba(0, 0, 0, .06);
            width: 50%;
            text-align: center;
        }

        .switch-toggle input.check-toggle-round-flat:checked ~ .off {
            color: #00b3f1;
        }

        .switch-toggle input.check-toggle-round-flat:checked ~ .on {
            color: #fff;
        }

        .switch-toggle > span.on {
            left: 0;
            padding-left: 2px;
            color: #00b3f1;
        }

        .switch-toggle > span.off {
            right: 0;
            padding-right: 4px;
            color: #fff;
        }

        .switch-toggle .check-toggle {
            position: absolute;
            margin-left: -9999px;
            visibility: hidden;
        }

        .switch-toggle .check-toggle + label {
            display: block;
            position: relative;
            cursor: pointer;
            outline: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .switch-toggle input.check-toggle-round-flat + label {
            padding: 2px;
            width: 144px;
            height: 35px;
            background-color: #00b3f1;
            -webkit-border-radius: 60px;
            -moz-border-radius: 60px;
            -ms-border-radius: 60px;
            -o-border-radius: 60px;
            border-radius: 60px;
        }

        .switch-toggle input.check-toggle-round-flat + label:before, input.check-toggle-round-flat + label:after {
            display: block;
            position: absolute;
            content: "";
        }

        .switch-toggle input.check-toggle-round-flat + label:after {
            top: 4px;
            left: 4px;
            bottom: 4px;
            width: 70px;
            background-color: #fff;
            -webkit-border-radius: 52px;
            -moz-border-radius: 52px;
            -ms-border-radius: 52px;
            -o-border-radius: 52px;
            border-radius: 52px;
            -webkit-transition: margin 0.2s;
            -moz-transition: margin 0.2s;
            -o-transition: margin 0.2s;
            transition: margin 0.2s;
        }

        .switch-toggle input.check-toggle-round-flat + label:after {
            top: 4px;
            left: 4px;
            bottom: 4px;
            width: 70px;
            background-color: #fff;
            -webkit-border-radius: 52px;
            -moz-border-radius: 52px;
            -ms-border-radius: 52px;
            -o-border-radius: 52px;
            border-radius: 52px;
            -webkit-transition: margin 0.2s;
            -moz-transition: margin 0.2s;
            -o-transition: margin 0.2s;
            transition: margin 0.2s;
        }

            /* .switch-toggle input.check-toggle-round-flat:checked + label {
            } */

        .switch-toggle input.check-toggle-round-flat:checked + label:after {
            margin-left: 65px;
        }


        .our-register,
        .our-login {
            background: #ffece773;
        }
    </style>

    <!-- Hidden Google Translate Element -->
    <div id="google_translate_element" style="display: none;"></div>

    <script type="text/javascript">
        function googleTranslateElementInit() {
          new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,it',
            autoDisplay: false
          }, 'google_translate_element');
        }

        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function () {
          const toggle = document.getElementById('language-toggle');

          // 🔁 Sync toggle switch with current language
          function syncToggleWithLanguage() {
            const currentLang = getCurrentGoogleLanguage();
            toggle.checked = (currentLang === 'it');
          }

          // 🧠 Detect language from Google Translate cookie
          function getCurrentGoogleLanguage() {
            const match = document.cookie.match(/googtrans=\/[a-z]{2}\/([a-z]{2})/);
            return match ? match[1] : 'en';
          }

          // 📌 Change language when toggle is flipped
          toggle.addEventListener('change', function () {
            const selectedLang = this.checked ? 'it' : 'en';
            const select = document.querySelector("select.goog-te-combo");
            if (select) {
              select.value = selectedLang;
              select.dispatchEvent(new Event("change"));
            }
          });

          // 🔄 Sync the toggle with the language after a short delay
          setTimeout(syncToggleWithLanguage, 500); // Small delay to ensure Translate loads
        });
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
    @stack('scripts')
    @stack('custom-scripts')

    <!-- Chat Sidebar Component (for authenticated users) -->
    @auth
        @livewire('chat-sidebar')
    @endauth
</body>
</html>
