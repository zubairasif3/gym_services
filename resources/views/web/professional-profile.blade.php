@extends('web.layouts.app')

@section('title', $user->name . ' ' . $user->surname . ' - Professional Profile')

@section('content')
<style>
.profile-wallpaper {
    height: 350px;
    background-size: cover;
    background-position: center;
    background-color: #1b1b18;
    position: relative;
}

.profile-avatar-section {
    margin-top: -80px;
    position: relative;
    z-index: 10;
}

.profile-avatar-large {
    width: 150px;
    height: 150px;
    border: 5px solid white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

.service-gallery-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
</style>

<!-- Profile Wallpaper -->
<div class="profile-wallpaper" 
     @if($user->profile && $user->profile->wallpaper_image)
         style="background-image: url('{{ asset('storage/' . $user->profile->wallpaper_image) }}');"
     @endif>
</div>

<!-- Profile Content -->
<section class="our-dashbord pt-0 pb50">
    <div class="container">
        <!-- Avatar & Basic Info -->
        <div class="profile-avatar-section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="bg-white p-4 shadow rounded">
                        <div class="row align-items-center">
                            <!-- Avatar -->
                            <div class="col-auto">
                                @if($user->avatar_url)
                                    <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                         class="rounded-circle profile-avatar-large" 
                                         alt="{{ $user->name }}">
                                @else
                                    <div class="rounded-circle profile-avatar-large bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="font-size: 48px; font-weight: bold;">
                                        {{ $user->initials }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Name & Stats -->
                            <div class="col">
                                <h2 class="mb-1">{{ $user->name }} {{ $user->surname }}</h2>
                                <p class="text-muted mb-2">@<span>{{ $user->username }}</span></p>
                                
                                @if($user->profile && $user->profile->bio)
                                    <p class="mb-3">{{ $user->profile->bio }}</p>
                                @endif
                                
                                <!-- Stats -->
                                <div class="d-flex gap-4">
                                    <div>
                                        <strong class="h5">{{ $user->followers_count }}</strong>
                                        <span class="text-muted ms-1">Followers</span>
                                    </div>
                                    <div>
                                        <strong class="h5">{{ $user->following_count }}</strong>
                                        <span class="text-muted ms-1">Following</span>
                                    </div>
                                    <div>
                                        <strong class="h5">{{ $user->gigs->count() }}</strong>
                                        <span class="text-muted ms-1">Services</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="col-auto">
                                <div class="d-flex flex-column gap-2">
                                    @auth
                                        @if(auth()->id() !== $user->id)
                                            @livewire('follow-button', ['user' => $user], key('follow-profile-' . $user->id))
                                            
                                            <button class="btn btn-primary" onclick="openChatWith({{ $user->id }})">
                                                <i class="far fa-envelope me-1"></i> Message
                                            </button>
                                        @else
                                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                                <i class="fas fa-edit me-1"></i> Edit Profile
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('web.login') }}" class="btn btn-primary">
                                            <i class="far fa-envelope me-1"></i> Message
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- About Section -->
        @if($user->profile && $user->profile->about)
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white p-4 shadow-sm rounded">
                    <h4 class="mb-3"><i class="far fa-user-circle text-primary me-2"></i> About</h4>
                    <p class="text-muted" style="white-space: pre-line;">{{ $user->profile->about }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Skills Section -->
        @if($user->profile && $user->profile->skills && count(json_decode($user->profile->skills, true) ?? []) > 0)
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white p-4 shadow-sm rounded">
                    <h4 class="mb-3"><i class="fas fa-star text-primary me-2"></i> Skills</h4>
                    <div class="skills-container">
                        @foreach(json_decode($user->profile->skills, true) as $skill)
                            <span class="skill-badge">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Media Gallery Section -->
        @if($user->activeProfileMedia && $user->activeProfileMedia->count() > 0)
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white p-4 shadow-sm rounded">
                    <h4 class="mb-4">
                        <i class="fas fa-photo-video text-primary me-2"></i> 
                        Media Gallery ({{ $user->activeProfileMedia->count() }})
                    </h4>
                    <div class="profile-media-slider-wrapper">
                        <button type="button" class="profile-slider-nav prev" onclick="slideProfileMedia(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="profile-media-slider-container">
                            <div class="media-gallery-container" id="profileMediaTrack">
                                @foreach($user->activeProfileMedia as $media)
                                    <div class="media-gallery-item" 
                                         data-media-id="{{ $media->id }}"
                                         data-media-type="{{ $media->media_type }}"
                                         data-media-url="{{ asset('storage/' . $media->file_path) }}">
                                        @if($media->media_type === 'image')
                                            <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                 alt="Profile media"
                                                 loading="lazy">
                                            <div class="media-type-badge">
                                                <i class="far fa-image"></i> Image
                                            </div>
                                        @else
                                            <video src="{{ asset('storage/' . $media->file_path) }}" 
                                                   muted 
                                                   loop
                                                   preload="metadata">
                                            </video>
                                            <div class="media-type-badge video">
                                                <i class="far fa-video"></i>
                                                @if($media->duration)
                                                    {{ $media->duration }}s
                                                @endif
                                            </div>
                                            <div class="media-play-icon">
                                                <i class="fas fa-play"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="profile-slider-nav next" onclick="slideProfileMedia(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Services Gallery -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="bg-white p-4 shadow-sm rounded">
                    <h4 class="mb-4"><i class="fas fa-briefcase text-primary me-2"></i> Services ({{ $user->gigs->count() }})</h4>
                    
                    @if($user->gigs->count() > 0)
                        <div class="row">
                            @foreach($user->gigs as $gig)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card service-gallery-item h-100">
                                        <!-- Service Image -->
                                        <a href="{{ route('gigs.show', $gig->slug) }}">
                                            @if($gig->images && $gig->images->count() > 0)
                                                <img src="{{ asset('storage/' . $gig->images->first()->image_path) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $gig->title }}"
                                                     style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                     style="height: 200px;">
                                                    <i class="far fa-image text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                        </a>
                                        
                                        <!-- Service Info -->
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="{{ route('gigs.show', $gig->slug) }}" 
                                                   class="text-dark text-decoration-none">
                                                    {{ Str::limit($gig->title, 50) }}
                                                </a>
                                            </h6>
                                            <p class="card-text small text-muted">
                                                {{ Str::limit(strip_tags($gig->description), 80) }}
                                            </p>
                                            
                                            <!-- Price & Rating -->
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="fw-bold text-primary">
                                                    €{{ number_format($gig->starting_price, 2) }}
                                                </span>
                                                @if($gig->rating > 0)
                                                    <span class="text-warning">
                                                        <i class="fas fa-star"></i> {{ number_format($gig->rating, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- View Button -->
                                        <div class="card-footer bg-transparent border-top-0">
                                            <a href="{{ route('gigs.show', $gig->slug) }}" 
                                               class="btn btn-sm btn-primary w-100">
                                                View Service
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="far fa-briefcase text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No services yet</h5>
                            <p class="text-muted">This professional hasn't added any services yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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

@endsection

@push('scripts')
<script>
    function openChatWith(userId) {
        Livewire.dispatch('open-chat-sidebar', { userId: userId });
    }
    
    // Profile Media Slider
    let profileSliderPosition = 0;
    
    function slideProfileMedia(direction) {
        const track = document.getElementById('profileMediaTrack');
        if (!track) return;
        
        const items = track.querySelectorAll('.media-gallery-item');
        if (items.length === 0) return;
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const slideDistance = itemWidth + gap;
        const containerWidth = track.parentElement.offsetWidth;
        const totalWidth = (itemWidth + gap) * items.length - gap;
        const maxScroll = Math.max(0, totalWidth - containerWidth);
        
        profileSliderPosition += direction * slideDistance;
        profileSliderPosition = Math.max(0, Math.min(profileSliderPosition, maxScroll));
        
        track.style.transform = `translateX(-${profileSliderPosition}px)`;
        updateProfileSliderButtons();
    }
    
    function updateProfileSliderButtons() {
        const prevBtn = document.querySelector('.profile-slider-nav.prev');
        const nextBtn = document.querySelector('.profile-slider-nav.next');
        const track = document.getElementById('profileMediaTrack');
        
        if (!prevBtn || !nextBtn || !track) return;
        
        const items = track.querySelectorAll('.media-gallery-item');
        if (items.length === 0) {
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            return;
        }
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const containerWidth = track.parentElement.offsetWidth;
        const totalWidth = (itemWidth + gap) * items.length - gap;
        const maxScroll = Math.max(0, totalWidth - containerWidth);
        
        prevBtn.disabled = profileSliderPosition === 0;
        nextBtn.disabled = profileSliderPosition >= maxScroll;
    }
    
    // Media Gallery Lightbox
    let currentMediaIndex = 0;
    let mediaItems = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize slider buttons
        updateProfileSliderButtons();
        
        // Collect all media items
        const galleryItems = document.querySelectorAll('.media-gallery-item');
        mediaItems = Array.from(galleryItems).map(item => ({
            type: item.dataset.mediaType,
            url: item.dataset.mediaUrl
        }));
        
        // Add click handlers
        galleryItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                openLightbox(index);
            });
            
            // Auto-play video on hover
            if (item.dataset.mediaType === 'video') {
                const video = item.querySelector('video');
                if (video) {
                    item.addEventListener('mouseenter', () => video.play());
                    item.addEventListener('mouseleave', () => {
                        video.pause();
                        video.currentTime = 0;
                    });
                }
            }
        });
        
        // Close lightbox on background click
        document.getElementById('mediaLightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('mediaLightbox');
            if (lightbox.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    navigateLightbox(-1);
                } else if (e.key === 'ArrowRight') {
                    navigateLightbox(1);
                }
            }
        });
        
        // Handle window resize for slider
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                profileSliderPosition = 0;
                const track = document.getElementById('profileMediaTrack');
                if (track) {
                    track.style.transform = 'translateX(0)';
                    updateProfileSliderButtons();
                }
            }, 250);
        });
    });
    
    function openLightbox(index) {
        currentMediaIndex = index;
        const lightbox = document.getElementById('mediaLightbox');
        const mediaContainer = document.getElementById('lightboxMedia');
        const media = mediaItems[currentMediaIndex];
        
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
        
        // Update nav button visibility
        updateNavButtons();
    }
    
    function closeLightbox() {
        const lightbox = document.getElementById('mediaLightbox');
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
            currentMediaIndex = mediaItems.length - 1;
        } else if (currentMediaIndex >= mediaItems.length) {
            currentMediaIndex = 0;
        }
        
        openLightbox(currentMediaIndex);
    }
    
    function updateNavButtons() {
        const prevBtn = document.querySelector('.lightbox-nav.prev');
        const nextBtn = document.querySelector('.lightbox-nav.next');
        
        if (mediaItems.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }
</script>
@endpush

