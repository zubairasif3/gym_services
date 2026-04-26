@extends('web.layouts.app')

@section('title', $user->name . ' ' . $user->surname . ' - Professional Profile')

@section('content')
<style>
    .profile-wallpaper {
        height: 300px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .professional-header-section {
        margin-bottom: 30px;
    }

    .profile-info-overlay {
        z-index: 10;
    }

    .profile-avatar-large img {
        object-fit: cover;
    }

    .profile-info h3 {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .profile-info p {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .skill-badge {
        display: inline-block;
        padding: 8px 16px;
        margin: 5px;
        background: #f0f2f5;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    /* Info Box Styles */
    .info-box {
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
        border-left: 3px solid #00b3f1;
        transition: all 0.3s ease;
    }

    .info-box:hover {
        background: #e9ecef;
        border-left-width: 4px;
    }

    .info-box i {
        font-size: 1.2rem;
    }

    /* Service Gallery / Media Carousel Styles */
    /* .service-gallery {
        margin-bottom: 2rem;
    } */

    .media-carousel-wrapper {
        position: relative;
        padding: 0 50px;
    }

    .media-carousel-container {
        overflow: hidden;
        border-radius: 12px;
    }

    .media-carousel-track {
        display: flex;
        gap: 20px;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .media-carousel-item {
        flex: 0 0 auto;
        width: 100%;
    }

    .media-content {
        position: relative;
        width: 100%;
        height: 500px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        background: #000;
    }

    .media-content img,
    .media-content video {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .video-play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(0, 179, 241, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        pointer-events: none;
    }

    .video-duration {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
    }

    .carousel-nav-btn {
        position: absolute;
        top: 40%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: 2px solid #00b3f1;
        color: #00b3f1;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .carousel-nav-btn:hover:not(:disabled) {
        background: #00b3f1;
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    .carousel-nav-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .carousel-nav-btn.prev {
        left: 0;
    }

    .carousel-nav-btn.next {
        right: 0;
    }

    .service-reactions {
        margin-bottom: 0;
        margin-top: 2rem;
    }
    
    .media-reaction-container {
        margin-bottom: 2.5rem;
    }

    /* Emoji Reaction Bar Styles */
    /* .reaction-emoji-bar {
        background: white;
        border-radius: 50px;
        padding: 8px 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: inline-flex;
        gap: 5px;
    } */

    .emoji-bar-container {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .emoji-bar-item {
        background: white;
        border: 2px solid transparent;
        border-radius: 30px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        /* flex-direction: column; */
        align-items: center;
        gap: 2px;
        min-width: 50px;
    }

    .emoji-bar-item:hover {
        transform: translateY(-3px);
        border-color: #00b3f1;
        background: #f0f9ff;
    }

    .emoji-bar-item.active {
        border-color: #00b3f1;
        background: linear-gradient(135deg, #e3f5ff 0%, #f0f9ff 100%);
    }

    .emoji-icon-bar {
        font-size: 20px;
        line-height: 1;
    }

    .emoji-count-bar {
        font-size: 0.7rem;
        font-weight: 700;
        color: #00b3f1;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .emoji-bar-item {
            min-width: 45px;
            padding: 6px 10px;
        }
        
        .emoji-icon-bar {
            font-size: 1.3rem;
        }
        
        .emoji-count-bar {
            font-size: 0.65rem;
        }
    }

    /* Media Thumbnails Styles */
    .media-thumbnails-container {
        margin-top: 3rem;
    }

    .media-thumbnails-grid {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .media-thumbnail-card {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .media-thumbnail-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        border-color: #00b3f1;
    }

    .media-thumbnail-card.active {
        border-color: #00b3f1;
        box-shadow: 0 4px 12px rgba(0, 179, 241, 0.4);
    }

    .media-thumbnail-card img,
    .media-thumbnail-card video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumbnail-video-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 24px;
        height: 24px;
        background: rgba(0, 179, 241, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10px;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .media-thumbnail-card {
            width: 70px;
            height: 70px;
        }
        
        .thumbnail-video-icon {
            width: 20px;
            height: 20px;
            font-size: 8px;
        }
    }

    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }

    .service-card {
        transition: all 0.3s ease;
    }

    .service-card:hover {
        border-color: #00b3f1 !important;
    }

    /* Media Gallery Styles */
    .profile-media-slider-wrapper {
        position: relative;
        padding: 0 50px;
    }

    .profile-media-slider-container {
        overflow: hidden;
        border-radius: 12px;
    }

    .media-gallery-container {
        display: flex;
        gap: 20px;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .media-gallery-item {
        flex: 0 0 auto;
        width: 250px;
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        aspect-ratio: 1;
        background: #f8f9fa;
        cursor: pointer;
    }

    .media-gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .media-gallery-item img,
    .media-gallery-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: 2px solid #00b3f1;
        color: #00b3f1;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .profile-slider-nav:hover:not(:disabled) {
        background: #00b3f1;
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 16px rgba(0, 179, 241, 0.4);
    }

    .profile-slider-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #dee2e6;
        color: #6c757d;
    }

    .profile-slider-nav.prev {
        left: 0;
    }

    .profile-slider-nav.next {
        right: 0;
    }

    @media (max-width: 768px) {
        .profile-media-slider-wrapper {
            padding: 0 40px;
        }
        
        .media-gallery-item {
            width: 200px;
        }
        
        .profile-slider-nav {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .profile-media-slider-wrapper {
            padding: 0 35px;
        }
        
        .media-gallery-item {
            width: 180px;
        }
        
        .profile-slider-nav {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
    }

    .media-type-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 179, 241, 0.9);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .media-type-badge.video {
        background: rgba(220, 53, 69, 0.9);
    }

    .media-play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(0, 179, 241, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        pointer-events: none;
        transition: all 0.3s ease;
    }

    .media-gallery-item:hover .media-play-icon {
        transform: translate(-50%, -50%) scale(1.1);
        background: rgba(0, 179, 241, 1);
    }

    /* Lightbox Styles */
    .media-lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .media-lightbox.active {
        display: flex;
    }

    .lightbox-content {
        max-width: 90vw;
        max-height: 90vh;
        position: relative;
    }

    .lightbox-content img,
    .lightbox-content video {
        max-width: 100%;
        max-height: 90vh;
        border-radius: 8px;
    }

    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #333;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .lightbox-close:hover {
        background: #00b3f1;
        color: white;
        transform: rotate(90deg);
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: #333;
        font-size: 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        justify-content: center;
        align-items: center;
    }

    .lightbox-nav:hover {
        background: #00b3f1;
        color: white;
    }

    .lightbox-nav.prev {
        left: -70px;
    }

    .lightbox-nav.next {
        right: -70px;
    }
    .professional-header-section {
        padding: 5px 0 20px !important;
        position: relative;
    }

    /* Pricing Card Styles */
    .pricing-card {
        border: none !important;
        border-radius: 12px;
    }

    .service-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-item {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-weight: 500;
        color: #212529;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .service-item:hover {
        background-color: #e9ecef;
    }

    .service-item.active {
        background-color: #f8f9fa;
        border-left: 4px solid #00b3f1;
        padding-left: 13px;
    }

    .pricing-card h3 {
        font-weight: 700;
        font-size: 2rem;
    }

    .pricing-card h6 {
        font-weight: 600;
        color: #212529;
    }

    .pricing-card .list-unstyled li {
        display: flex;
        align-items: center;
        color: #495057;
    }

    .pricing-card .list-unstyled li i {
        font-size: 1.1rem;
    }

    .pricing-card .btn-lg {
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .pricing-card .btn-primary:hover {
        background-color: #0099d6;
        border-color: #0099d6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 179, 241, 0.3);
    }

    .pricing-card .btn-outline-primary {
        border-width: 2px;
        font-weight: 600;
    }

    .pricing-card .btn-outline-primary:hover {
        background-color: #00b3f1;
        border-color: #00b3f1;
        transform: translateY(-2px);
    }

    /* Fix z-index for share dropdown to appear above services */
    .share-button-wrapper {
        position: relative;
        z-index: 9999 !important;
    }
    
    .share-dropdown {
        z-index: 10000 !important;
        position: fixed !important;
    }
    
    .pricing-card.sticky-top {
        z-index: 10;
    }
    
    /* Ensure services section doesn't create stacking context */
    .col-lg-4 {
        position: relative;
        z-index: 1;
    }
    
    .col-lg-8 {
        position: relative;
        z-index: 1;
    }
</style>

<!-- Professional Header Section -->
<section class="professional-header-section mt-5 pt-4">
    <div class="container-fluid p-0">
        <!-- Wallpaper Background -->
        <div class="profile-wallpaper position-relative" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ $user->profile && $user->profile->wallpaper_image ? asset('storage/' . $user->profile->wallpaper_image) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&h=400&fit=crop' }}'); 
                    height: 300px; 
                    background-size: cover; 
                    background-position: center;">
            
            <!-- Profile Info Overlay -->
            <div class="container h-100">
                <div class="profile-info-overlay position-absolute d-flex align-items-end" style="bottom: 20px; left: 0; right: 0;">
                    <div class="container">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-4">
                                    <!-- Profile Avatar -->
                                    <div class="profile-avatar-large">
                                        @if($user->avatar_url)
                                            <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                                 class="rounded-circle border border-5 border-white shadow-lg" 
                                                 width="120" 
                                                 height="120" 
                                                 alt="{{ $user->name }}">
                                        @else
                                            <div class="rounded-circle border border-5 border-white shadow-lg bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                                {{ $user->initials }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Profile Name & Info -->
                                    <div class="profile-info text-white">
                                        <h3 class="mb-2 text-white fw-bold notranslate" translate="no">{{ $user->name }} {{ $user->surname ?? '' }}</h3>
                                        {{-- <p class="mb-1 text-white-50" style="font-size: 0.95rem;">
                                            <i class="flaticon-goal me-2"></i>
                                            <span>Lingua: {{ $user->profile->languages ?? 'English' }}</span>
                                        </p>
                                        <p class="mb-0 text-white-50" style="font-size: 0.95rem;">
                                            <i class="flaticon-tracking me-2"></i>
                                            <span>Posizione: {{ $user->profile->city ?? 'New York' }}</span>
                                        </p> --}}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="col-md-4">
                                <div class="d-flex gap-3 justify-content-end align-items-center mb-3">
                                    @auth
                                        @if(auth()->id() !== $user->id)
                                            <!-- Follow Button -->
                                            @livewire('follow-button', ['user' => $user], key('follow-profile-' . $user->id))
                                        @endif
                                    @else
                                        <!-- Follow Button -->
                                        @livewire('follow-button', ['user' => $user], key('follow-profile-' . $user->id))
                                    @endauth
                                    
                                    <!-- Share Button (Always visible) -->
                                    @livewire('share-profile-button', ['user' => $user], key('share-profile-' . $user->id))
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="our-dashbord pt-0 pb50">
    <div class="container">
        <div class="row mt-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Info Boxes -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box">
                            <i class="fas fa-user-friends text-primary me-2"></i>
                            <strong>Followers:</strong> {{ \App\Models\User::followersCount($user->id) }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <i class="flaticon-goal text-primary me-2"></i>
                            <strong>Language:</strong> {{ $user->profile->languages ?? 'English' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <i class="flaticon-tracking text-primary me-2"></i>
                            <strong>Location:</strong> {{ $user->profile->city ?? 'Not specified' }}
                        </div>
                    </div>
                </div>
                
                <!-- Photos and Videos Section -->
                @if($user->activeProfileMedia && $user->activeProfileMedia->count() > 0)
                <div class="service-gallery">
                    <h5 class="mb-3">
                        <i class="far fa-images text-primary me-2"></i> Photos and Videos
                    </h5>
                    <div class="media-carousel-wrapper position-relative">
                        <button type="button" class="carousel-nav-btn prev" onclick="slideMedia(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="media-carousel-container">
                            <div class="media-carousel-track" id="mediaCarouselTrack">
                                @foreach($user->activeProfileMedia as $media)
                                    <div class="media-carousel-item" data-media-id="{{ $media->id }}">
                                        <div class="media-content" onclick="openMediaLightbox({{ $loop->index }})">
                                            @if($media->media_type === 'image')
                                                <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                     alt="Profile media"
                                                     loading="lazy">
                                            @else
                                                <video src="{{ asset('storage/' . $media->file_path) }}" 
                                                       muted 
                                                       loop
                                                       preload="metadata">
                                                </video>
                                                <div class="video-play-overlay">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                                @if($media->duration)
                                                    <div class="video-duration">{{ $media->duration }}s</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <button type="button" class="carousel-nav-btn next" onclick="slideMedia(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Reaction Buttons Section (Below Photos) -->
                <div class="service-reactions mb-5">
                    @if($user->activeProfileMedia && $user->activeProfileMedia->count() > 0)
                        @foreach($user->activeProfileMedia as $index => $media)
                            <div class="media-reaction-container" id="reaction-{{ $index }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
                                @livewire('profile-media-reactions', ['mediaId' => $media->id], key('profile-reactions-' . $media->id))
                            </div>
                        @endforeach
                        
                        <!-- Media Thumbnails -->
                        <div class="media-thumbnails-container">
                            <div class="media-thumbnails-grid">
                                @foreach($user->activeProfileMedia as $index => $media)
                                    <div class="media-thumbnail-card {{ $index === 0 ? 'active' : '' }}" 
                                         onclick="goToMediaSlide({{ $index }})"
                                         data-media-id="{{ $media->id }}"
                                         id="thumbnail-{{ $index }}">
                                        @if($media->media_type === 'image')
                                            <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                 alt="Media {{ $index + 1 }}"
                                                 loading="lazy">
                                        @else
                                            <video src="{{ asset('storage/' . $media->file_path) }}" 
                                                   muted
                                                   preload="metadata">
                                            </video>
                                            <div class="thumbnail-video-icon">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                @else
                <div class="service-gallery mb-5">
                    <h5 class="mb-3">
                        <i class="far fa-images text-primary me-2"></i> Photos and Videos
                    </h5>
                    <div class="text-center py-5 bg-light rounded">
                        <i class="far fa-image text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No media available</p>
                    </div>
                </div>
                @endif
                
                <!-- About Section -->
                @if($user->profile && $user->profile->about)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="far fa-align-left text-primary me-2"></i> About
                    </h5>
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-0 text-muted" style="font-size: 1rem; line-height: 1.6;"> {{-- white-space: pre-wrap;--}}
                                {{ $user->profile->about }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Reviews Section -->
                <div class="reviews-section mb-5">
                    <h4 class="mb-4">
                        <i class="far fa-thumbs-up text-primary me-2"></i> Reviews
                        <span class="badge bg-primary ms-2">{{ $reviewStats['total'] }}</span>
                    </h4>
                    
                    @if($user->profileReviews && $user->profileReviews->count() > 0)
                        <!-- Show latest reviews -->
                        <div class="latest-reviews mb-4">
                            @foreach($user->profileReviews->take(3) as $review)
                                <div class="review-card border rounded p-3 mb-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="review-avatar">
                                            @if($review->reviewer->avatar_url)
                                                <img src="{{ asset('storage/' . $review->reviewer->avatar_url) }}" 
                                                     class="rounded-circle" 
                                                     width="50" 
                                                     height="50">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    {{ $review->reviewer->initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="review-content flex-grow-1">
                                            <h6 class="mb-1 notranslate" translate="no">{{ $review->reviewer->name }}</h6>
                                            <div class="text-warning mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-muted small mb-2">{{ $review->created_at->diffForHumans() }}</p>
                                            <p class="mb-0">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- See All Reviews Button -->
                        @if($reviewStats['total'] > 3)
                            <button class="btn btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#reviewsModal">
                                <i class="far fa-eye me-2"></i> See all Reviews ({{ $reviewStats['total'] }})
                            </button>
                        @endif
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="far fa-comment-slash text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No reviews yet. Be the first to review!</p>
                        </div>
                    @endif
                    
                    <!-- Leave Review Section -->
                    @auth
                        @if(auth()->id() !== $user->id)
                            <div class="mt-4">
                                @livewire('profile-review-form', ['profileUserId' => $user->id], key('profile-review-form-' . $user->id))
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <a href="{{ route('web.login') }}">Log in</a> to leave a review
                        </div>
                    @endauth
                </div>
            </div>
            
            <!-- Right Column - Services Tab -->
            <div class="col-lg-4">
                <!-- Services Pricing Card -->
                @if($user->services && $user->services->count() > 0)
                <div class="pricing-card card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <!-- Service List -->
                        <div class="service-list mb-3">
                            @foreach($user->services as $index => $service)
                                <div class="service-item {{ $index == 0 ? 'active' : '' }}" 
                                     data-service-index="{{ $index }}">
                                    {{ $service->title }}
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Service Content -->
                        <div class="tab-content">
                            @foreach($user->services as $index => $service)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                                     id="service-{{ $index }}">
                                    <h3 class="text-primary mb-3">€{{ number_format($service->price, 2) }}</h3>
                                    <h6 class="mb-2">{{ $service->title }}</h6>
                                    <p class="text-muted small mb-3">{!! strip_tags($service->description) !!}</p>
                                    {{-- <ul class="list-unstyled mb-3">
                                        <li class="mb-2">
                                            <i class="flaticon-sandclock text-primary me-2"></i>
                                            {{ $service->delivery }} Days Delivery
                                        </li>
                                    </ul> --}}
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 mt-4">
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <a href="{{ route('appointments.book', $user->username) }}" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="far fa-calendar-check me-2"></i> Book now
                                    </a>
                                    <button class="btn btn-outline-primary" 
                                            onclick="Livewire.dispatch('open-chat-sidebar', { userId: {{ $user->id }} })">
                                        <i class="far fa-paper-plane me-2"></i> Contact me
                                    </button>
                                @else
                                    <a href="{{ url('/admin') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-edit me-2"></i> Manage Services
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('web.login') }}" class="btn btn-primary btn-lg">
                                    <i class="far fa-calendar-check me-2"></i> Book now
                                </a>
                                <a href="{{ route('web.login') }}" class="btn btn-outline-primary">
                                    <i class="far fa-paper-plane me-2"></i> Contact me
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @else
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body text-center py-5">
                        <i class="far fa-briefcase text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-3 mb-0">No services available</p>
                    </div>
                </div>
                @endif
                
                <!-- Skills Section -->
                @if($user->profile && $user->profile->skills)
                    @php
                        $skills = is_string($user->profile->skills) 
                            ? (json_decode($user->profile->skills, true) ?? explode(',', $user->profile->skills))
                            : ($user->profile->skills ?? []);
                        $skills = is_array($skills) ? $skills : [];
                        $skills = array_filter(array_map('trim', $skills));
                    @endphp
                    @if(count($skills) > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <i class="far fa-star text-primary me-2"></i> Skills
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($skills as $skill)
                                    <span class="skill-badge">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
                
                <!-- Bio Section -->
                @if($user->profile && $user->profile->bio)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="far fa-quote-left text-primary me-2"></i> Bio
                        </h5>
                        <p class="mb-0 text-muted" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ $user->profile->bio }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        {{-- add review system --}}
    </div>
</section>

<!-- Media Lightbox -->
<div class="media-lightbox" id="mediaLightbox">
    <div class="lightbox-content">
        {{-- <button class="lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button> --}}
        <button class="lightbox-nav prev" onclick="navigateLightbox(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="lightbox-nav next" onclick="navigateLightbox(1)">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div id="lightboxMedia"></div>
    </div>
</div>

<!-- Reviews Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewsModalLabel">
                    <i class="far fa-thumbs-up text-primary me-2"></i> All Reviews ({{ $reviewStats['total'] }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($user->profileReviews && $user->profileReviews->count() > 0)
                    @foreach($user->profileReviews as $review)
                        <div class="review-card border rounded p-3 mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="review-avatar">
                                    @if($review->reviewer->avatar_url)
                                        <img src="{{ asset('storage/' . $review->reviewer->avatar_url) }}" 
                                             class="rounded-circle" 
                                             width="50" 
                                             height="50">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            {{ $review->reviewer->initials }}
                                        </div>
                                    @endif
                                </div>
                                <div class="review-content flex-grow-1">
                                    <h6 class="mb-1 notranslate" translate="no">{{ $review->reviewer->name }}</h6>
                                    <div class="text-warning mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-muted small mb-2">{{ $review->created_at->diffForHumans() }}</p>
                                    <p class="mb-0">{{ $review->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openChatWith(userId) {
        Livewire.dispatch('open-chat-sidebar', { userId: userId });
    }
    
    // Service item click handler
    document.addEventListener('DOMContentLoaded', function() {
        const serviceItems = document.querySelectorAll('.service-item');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        serviceItems.forEach(function(item) {
            item.addEventListener('click', function() {
                const serviceIndex = this.getAttribute('data-service-index');
                
                // Remove active class from all service items
                serviceItems.forEach(function(serviceItem) {
                    serviceItem.classList.remove('active');
                });
                
                // Add active class to clicked service item
                this.classList.add('active');
                
                // Hide all tab panes
                tabPanes.forEach(function(pane) {
                    pane.classList.remove('show', 'active');
                });
                
                // Show the corresponding tab pane
                const targetPane = document.getElementById('service-' + serviceIndex);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
    });
    
    // Media Carousel
    let currentMediaIndex = 0;
    let mediaItems = [];
    
    function slideMedia(direction) {
        const track = document.getElementById('mediaCarouselTrack');
        if (!track) return;
        
        const items = track.querySelectorAll('.media-carousel-item');
        if (items.length === 0) return;
        
        currentMediaIndex += direction;
        currentMediaIndex = Math.max(0, Math.min(currentMediaIndex, items.length - 1));
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const offset = currentMediaIndex * (itemWidth + gap);
        
        track.style.transform = `translateX(-${offset}px)`;
        updateCarouselButtons();
        updateReactionDisplay();
    }
    
    function updateReactionDisplay() {
        // Hide all reaction containers
        document.querySelectorAll('.media-reaction-container').forEach(container => {
            container.style.display = 'none';
        });
        
        // Show the reaction container for the current media
        const currentReaction = document.getElementById(`reaction-${currentMediaIndex}`);
        if (currentReaction) {
            currentReaction.style.display = 'block';
        }
        
        // Update thumbnail active states
        updateThumbnailStates();
    }
    
    function updateThumbnailStates() {
        // Remove active class from all thumbnails
        document.querySelectorAll('.media-thumbnail-card').forEach(thumb => {
            thumb.classList.remove('active');
        });
        
        // Add active class to current thumbnail
        const currentThumb = document.getElementById(`thumbnail-${currentMediaIndex}`);
        if (currentThumb) {
            currentThumb.classList.add('active');
        }
    }
    
    function goToMediaSlide(index) {
        const track = document.getElementById('mediaCarouselTrack');
        if (!track) return;
        
        const items = track.querySelectorAll('.media-carousel-item');
        if (items.length === 0) return;
        
        currentMediaIndex = index;
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const offset = currentMediaIndex * (itemWidth + gap);
        
        track.style.transform = `translateX(-${offset}px)`;
        updateCarouselButtons();
        updateReactionDisplay();
    }

    function focusMediaFromQueryParam() {
        const params = new URLSearchParams(window.location.search);
        const targetMediaId = params.get('media_id');
        if (!targetMediaId) return;

        const track = document.getElementById('mediaCarouselTrack');
        if (!track) return;

        const items = Array.from(track.querySelectorAll('.media-carousel-item'));
        if (items.length === 0) return;

        const targetIndex = items.findIndex((item) => item.dataset.mediaId === targetMediaId);
        if (targetIndex === -1) return;

        goToMediaSlide(targetIndex);
    }
    
    function updateCarouselButtons() {
        const prevBtn = document.querySelector('.carousel-nav-btn.prev');
        const nextBtn = document.querySelector('.carousel-nav-btn.next');
        const track = document.getElementById('mediaCarouselTrack');
        
        if (!prevBtn || !nextBtn || !track) return;
        
        const items = track.querySelectorAll('.media-carousel-item');
        if (items.length === 0) {
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            return;
        }
        
        prevBtn.disabled = currentMediaIndex === 0;
        nextBtn.disabled = currentMediaIndex >= items.length - 1;
    }
    
    // Media Lightbox
    let lightboxMediaItems = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize carousel buttons
        updateCarouselButtons();
        focusMediaFromQueryParam();
        
        // Collect all media items for lightbox
        const carouselItems = document.querySelectorAll('.media-carousel-item');
        carouselItems.forEach((item, index) => {
            const mediaContent = item.querySelector('.media-content');
            const img = mediaContent.querySelector('img');
            const video = mediaContent.querySelector('video');
            
            if (img) {
                lightboxMediaItems.push({
                    type: 'image',
                    url: img.src
                });
            } else if (video) {
                lightboxMediaItems.push({
                    type: 'video',
                    url: video.src
                });
                
                // Auto-play video on hover
                mediaContent.addEventListener('mouseenter', () => video.play());
                mediaContent.addEventListener('mouseleave', () => {
                    video.pause();
                    video.currentTime = 0;
                });
            }
        });
        
        // Close lightbox on background click
        const lightbox = document.getElementById('mediaLightbox');
        if (lightbox) {
            lightbox.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLightbox();
                }
            });
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('mediaLightbox');
            if (lightbox && lightbox.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    navigateLightbox(-1);
                } else if (e.key === 'ArrowRight') {
                    navigateLightbox(1);
                }
            }
        });
        
        // Handle window resize for carousel
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                currentMediaIndex = 0;
                const track = document.getElementById('mediaCarouselTrack');
                if (track) {
                    track.style.transform = 'translateX(0)';
                    updateCarouselButtons();
                }
            }, 250);
        });
    });
    
    function openMediaLightbox(index) {
        const lightbox = document.getElementById('mediaLightbox');
        if (!lightbox) return;
        
        const mediaContainer = document.getElementById('lightboxMedia');
        const media = lightboxMediaItems[index];
        
        if (!media) return;
        
        // Update current index
        currentMediaIndex = index;
        
        // Clear previous content
        mediaContainer.innerHTML = '';
        
        // Create media element
        if (media.type === 'image') {
            const img = document.createElement('img');
            img.src = media.url;
            img.alt = 'Profile media';
            mediaContainer.appendChild(img);
        } else {
            const video = document.createElement('video');
            video.src = media.url;
            video.controls = true;
            video.autoplay = true;
            video.style.maxWidth = '100%';
            video.style.maxHeight = '90vh';
            video.style.borderRadius = '8px';
            mediaContainer.appendChild(video);
        }
        
        // Show lightbox
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Update reaction display
        updateReactionDisplay();
        updateLightboxNavButtons();
    }
    
    function closeLightbox() {
        const lightbox = document.getElementById('mediaLightbox');
        if (!lightbox) return;
        
        const mediaContainer = document.getElementById('lightboxMedia');
        
        // Stop any playing video
        const video = mediaContainer.querySelector('video');
        if (video) {
            video.pause();
        }
        
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function navigateLightbox(direction) {
        currentMediaIndex += direction;
        
        // Loop around
        if (currentMediaIndex < 0) {
            currentMediaIndex = lightboxMediaItems.length - 1;
        } else if (currentMediaIndex >= lightboxMediaItems.length) {
            currentMediaIndex = 0;
        }
        
        // Update carousel position
        const track = document.getElementById('mediaCarouselTrack');
        if (track) {
            const items = track.querySelectorAll('.media-carousel-item');
            if (items.length > 0) {
                const itemWidth = items[0].offsetWidth;
                const gap = 20;
                const offset = currentMediaIndex * (itemWidth + gap);
                track.style.transform = `translateX(-${offset}px)`;
            }
        }
        
        updateCarouselButtons();
        openMediaLightbox(currentMediaIndex);
    }
    
    function updateLightboxNavButtons() {
        const prevBtn = document.querySelector('.lightbox-nav.prev');
        const nextBtn = document.querySelector('.lightbox-nav.next');
        
        if (!prevBtn || !nextBtn) return;
        
        if (lightboxMediaItems.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }
</script>
@endpush

