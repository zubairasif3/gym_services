@extends('web.layouts.app')

@section('title', $gig->title)

@section('content')

<!-- Professional Profile Header Section -->
<section class="professional-header-section mt-5 pt-4">
    <div class="container-fluid p-0">
        <!-- Wallpaper Background -->
        <div class="profile-wallpaper position-relative" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ $gig->user->profile && $gig->user->profile->wallpaper_image ? asset('storage/' . $gig->user->profile->wallpaper_image) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&h=400&fit=crop' }}'); 
                    height: 300px; 
                    background-size: cover; 
                    background-position: center;">
            
            <!-- Profile Info Overlay -->
            <div class="container h-100">
                <div class="profile-info-overlay position-absolute d-flex align-items-end" style="bottom: 20px; left: 0; right: 0;">
                    <div class="container">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <div class="d-flex align-items-end gap-4">
                                    <!-- Profile Avatar -->
                                    <div class="profile-avatar-large">
                                        @if($gig->user->avatar_url)
                                            <img src="{{ asset('storage/' . $gig->user->avatar_url) }}" 
                                                 class="rounded-circle border border-5 border-white shadow-lg" 
                                                 width="120" 
                                                 height="120" 
                                                 alt="{{ $gig->user->name }}">
                                        @else
                                            <div class="rounded-circle border border-5 border-white shadow-lg bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                                                {{ $gig->user->initials }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Profile Name & Info -->
                                    <div class="profile-info text-white mb-3">
                                        <h3 class="mb-2 text-white fw-bold">{{ $gig->user->name }} {{ $gig->user->surname ?? '' }}</h3>
                                        <p class="mb-1 text-white-50" style="font-size: 0.95rem;">
                                            <i class="flaticon-goal me-2"></i>
                                            <span>Lingua: {{ $gig->user->profile->languages ?? 'English' }}</span>
                                        </p>
                                        <p class="mb-0 text-white-50" style="font-size: 0.95rem;">
                                            <i class="flaticon-tracking me-2"></i>
                                            <span>Posizione: {{ $gig->user->profile->city ?? 'New York' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="col-md-4">
                                <div class="d-flex gap-3 justify-content-end align-items-center mb-3">
                                    <!-- Follow/Follower Card (Combined) -->
                                    @livewire('follow-button', ['user' => $gig->user], key('follow-gig-' . $gig->user->id))
                                    
                                    <!-- Share Button -->
                                    @livewire('share-button', ['gig' => $gig], key('share-gig-' . $gig->id))
                                    
                                    <!-- Save Button -->
                                    @livewire('save-button', ['gig' => $gig], key('save-gig-' . $gig->id))
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Details Section -->
<section class="service-details-section pt-5 pb-5">
    <div class="container">
        <div class="row">
            <!-- Left Column: Service Information -->
            <div class="col-lg-8">
                <!-- Service Title & Info -->
                <div class="service-title-section mb-4">
                    <h1 class="mb-3">{{ $gig->title }}</h1>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="flaticon-calendar text-primary me-2"></i>
                                <strong>Delivery Time:</strong> {{ $gig->delivery_time }} Days
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="flaticon-goal text-primary me-2"></i>
                                <strong>Language:</strong> {{ $gig->user->profile->languages ?? 'English' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <i class="flaticon-tracking text-primary me-2"></i>
                                <strong>Location:</strong> {{ $gig->user->profile->city ?? 'new York' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Gallery -->
                @if($gig->images && $gig->images->count() > 0)
                <div class="service-gallery mb-5">
                    <h4 class="mb-3">
                        <i class="far fa-images text-primary me-2"></i> Photos and Videos
                    </h4>
                    <div class="service-single-slider owl-carousel owl-theme">
                        @foreach($gig->images as $image)
                            <div class="item">
                                <div class="gallery-item position-relative">
                                    <img src="{{ file_exists(public_path('storage/' . $image->image_path)) ? asset('storage/' . $image->image_path) : 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&h=500&fit=crop' }}" 
                                         class="w-100 rounded" 
                                         alt="{{ $gig->title }}"
                                         style="max-height: 500px; object-fit: cover;">
                                    <button class="btn btn-light btn-sm position-absolute" 
                                            style="bottom: 10px; right: 10px;"
                                            onclick="openFullscreen(this)">
                                        <i class="fas fa-expand me-1"></i> Schermo intero
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Thumbnail Navigation -->
                    <div class="gallery-thumbnails mt-3 d-flex gap-2 flex-wrap">
                        @foreach($gig->images->take(6) as $index => $image)
                            <div class="thumbnail-item">
                                <img src="{{ file_exists(public_path('storage/' . $image->image_path)) ? asset('storage/' . $image->image_path) : 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=100&h=80&fit=crop' }}" 
                                     class="rounded" 
                                     width="100" 
                                     height="80" 
                                     style="object-fit: cover; cursor: pointer;"
                                     onclick="goToSlide({{ $index }})">
                            </div>
                        @endforeach
                        @if($gig->images->count() > 6)
                            <div class="thumbnail-item d-flex align-items-center justify-content-center bg-light rounded" 
                                 style="width: 100px; height: 80px;">
                                <span class="fw-bold">+{{ $gig->images->count() - 6 }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="service-gallery mb-5">
                    <h4 class="mb-3">
                        <i class="far fa-images text-primary me-2"></i> Photos and Videos
                    </h4>
                    <div class="service-single-slider owl-carousel owl-theme">
                        <div class="item">
                            <div class="gallery-item position-relative">
                                <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&h=500&fit=crop" 
                                     class="w-100 rounded" 
                                     alt="{{ $gig->title }}"
                                     style="max-height: 500px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- About Section -->
                <div class="service-about mb-5">
                    <h4 class="mb-3">
                        <i class="far fa-file-alt text-primary me-2"></i> About
                    </h4>
                    <div class="about-content">
                        <p class="text-muted">{!! nl2br(strip_tags($gig->about ?? $gig->description)) !!}</p>
                    </div>
                </div>
                
                <!-- Reviews Section -->
                <div class="reviews-section mb-5">
                    <h4 class="mb-4">
                        <i class="far fa-thumbs-up text-primary me-2"></i> Reviews
                        <span class="badge bg-primary ms-2">{{ $reviewStats['total'] }}</span>
                    </h4>
                    
                    @if($gig->reviews && $gig->reviews->count() > 0)
                        <!-- Show latest reviews -->
                        <div class="latest-reviews mb-4">
                            @foreach($gig->reviews->take(3) as $review)
                                <div class="review-card border rounded p-3 mb-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="review-avatar">
                                            @if($review->user->avatar_url)
                                                <img src="{{ asset('storage/' . $review->user->avatar_url) }}" 
                                                     class="rounded-circle" 
                                                     width="50" 
                                                     height="50">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    {{ $review->user->initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="review-content flex-grow-1">
                                            <h6 class="mb-1">{{ $review->user->name }}</h6>
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
                        <button class="btn btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#reviewsModal">
                            <i class="far fa-eye me-2"></i> See all Reviews ({{ $reviewStats['total'] }})
                        </button>
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="far fa-comment-slash text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">No reviews yet. Be the first to review!</p>
                        </div>
                    @endif
                    
                    <!-- Leave Review Section -->
                    @auth
                        <div class="mt-4">
                            @livewire('review-form', ['gigId' => $gig->id], key('review-form-' . $gig->id))
                        </div>
                    @else
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <a href="{{ route('web.login') }}">Log in</a> to leave a review
                        </div>
                    @endauth
                </div>
            </div>
            
            <!-- Right Column: Pricing & Professional Info -->
            <div class="col-lg-4">
                <div class="sticky-sidebar" style="position: sticky; top: 100px;">
                    <!-- Pricing Packages -->
                    @if($gig->packages && $gig->packages->count() > 0)
                    <div class="pricing-card card shadow-sm mb-4">
                        <div class="card-body">
                            <!-- Package Tabs -->
                            <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                                @foreach($gig->packages as $index => $package)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}" 
                                                id="package-{{ $index }}-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#package-{{ $index }}" 
                                                type="button">
                                            {{ $package->title }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            
                            <!-- Package Content -->
                            <div class="tab-content">
                                @foreach($gig->packages as $index => $package)
                                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                                         id="package-{{ $index }}">
                                        <h3 class="text-primary mb-3">€{{ number_format($package->price, 2) }}</h3>
                                        <h6 class="mb-2">{{ $package->title }}</h6>
                                        <p class="text-muted small mb-3">{!! strip_tags($package->description) !!}</p>
                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-2">
                                                <i class="flaticon-sandclock text-primary me-2"></i>
                                                {{ $package->delivery_time }} Days Delivery
                                            </li>
                                            @if($package->revision_limit > 0)
                                                <li class="mb-2">
                                                    <i class="flaticon-recycle text-primary me-2"></i>
                                                    {{ $package->revision_limit }} Revisions
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-primary btn-lg">
                                    <i class="far fa-calendar-check me-2"></i> Book now
                                </button>
                                @auth
                                    <button class="btn btn-outline-primary" 
                                            onclick="Livewire.dispatch('open-chat-sidebar', { userId: {{ $gig->user->id }} })">
                                        <i class="far fa-paper-plane me-2"></i> Contact me
                                    </button>
                                @else
                                    <a href="{{ route('web.login') }}" class="btn btn-outline-primary">
                                        <i class="far fa-paper-plane me-2"></i> Contact me
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Professional Card -->
                    <div class="professional-card card shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($gig->user->avatar_url)
                                    <img src="{{ asset('storage/' . $gig->user->avatar_url) }}" 
                                         class="rounded-circle mb-2" 
                                         width="80" 
                                         height="80">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" 
                                         style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ $gig->user->initials }}
                                    </div>
                                @endif
                                <h6 class="mb-1">{{ $gig->user->name }} {{ $gig->user->surname ?? '' }}</h6>
                                <p class="text-muted small mb-0">@<span>{{ $gig->user->username ?? strtolower($gig->user->name) }}</span></p>
                            </div>
                            
                            <hr>
                            
                            <div class="professional-stats">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Location:</span>
                                    <span class="fw-bold">{{ $gig->user->profile->city ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Language:</span>
                                    <span class="fw-bold">{{ $gig->user->profile->languages ?? 'English' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Services:</span>
                                    <span class="fw-bold">{{ $gig->user->gigs->count() }}</span>
                                </div>
                            </div>
                            
                            <hr>
                            
                            @if($gig->user->isProfessional())
                                <a href="{{ route('professional.profile', $gig->user->username ?? $gig->user->id) }}" 
                                   class="btn btn-outline-dark w-100">
                                    <i class="far fa-user me-2"></i> View Full Profile
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Services -->
@if($relatedGigs && $relatedGigs->count() > 0)
<section class="related-services-section pt-5 pb-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-2">Related Services</h3>
                <p class="text-muted">Discover similar services from other professionals</p>
            </div>
        </div>
        <div class="row">
            @foreach($relatedGigs as $relatedGig)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 service-card">
                        <div class="position-relative">
                            @if($relatedGig->images && $relatedGig->images->count() > 0)
                                <img src="{{ asset('storage/' . $relatedGig->images->first()->image_path) }}" 
                                     class="card-img-top" 
                                     alt="{{ $relatedGig->title }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="far fa-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">{{ $relatedGig->subcategory->name ?? 'Service' }}</p>
                            <h6 class="card-title">
                                <a href="{{ route('gigs.show', $relatedGig->slug) }}" 
                                   class="text-dark text-decoration-none">
                                    {{ Str::limit($relatedGig->title, 60) }}
                                </a>
                            </h6>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="fw-bold me-1">{{ number_format($relatedGig->rating ?? 0, 1) }}</span>
                                <span class="text-muted small">({{ $relatedGig->ratings_count ?? 0 }})</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @if($relatedGig->user->avatar_url)
                                    <img src="{{ asset('storage/' . $relatedGig->user->avatar_url) }}" 
                                         class="rounded-circle me-2" 
                                         width="30" 
                                         height="30">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width: 30px; height: 30px; font-size: 0.75rem;">
                                        {{ $relatedGig->user->initials }}
                                    </div>
                                @endif
                                <small>{{ $relatedGig->user->name }}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">From</small>
                                <div class="fw-bold text-primary">€{{ number_format($relatedGig->starting_price, 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Reviews Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewsModalLabel">
                    <i class="far fa-thumbs-up text-primary me-2"></i> All Reviews
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @livewire('reviews-list', ['gigId' => $gig->id], key('reviews-list-' . $gig->id))
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/service-detail.css') }}">
<style>
    /* Additional page-specific styles */
    .toast-notification {
        position: fixed;
        top: 100px;
        right: 20px;
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
    }
    
    .toast-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
    
    .toast-notification.success {
        border-left: 4px solid #28a745;
    }
    
    .toast-notification.error {
        border-left: 4px solid #dc3545;
    }
    
    .toast-notification.info {
        border-left: 4px solid #17a2b8;
    }
    
    body.loading::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
        z-index: 9998;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/service-detail.js') }}"></script>
<script>
    // Page-specific JavaScript
    $(document).ready(function() {
        // Mark first thumbnail as active
        $('.thumbnail-item').first().addClass('active');
        
        // Handle Book Now button
        $('.btn-primary:contains("Book now")').on('click', function(e) {
            e.preventDefault();
            alert('Booking functionality will be implemented soon!');
            // TODO: Integrate with booking/order system
        });
    });
</script>
@endpush

@endsection
