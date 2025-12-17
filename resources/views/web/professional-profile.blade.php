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
                                    @livewire('follow-button', ['user' => $user], key('follow-profile-' . $user->id))
                                    
                                    @auth
                                        @if(auth()->id() !== $user->id)
                                            <button class="btn btn-primary" onclick="openChatWith({{ $user->id }})">
                                                <i class="far fa-envelope me-1"></i> Message
                                            </button>
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
                                            @if($gig->images && count(json_decode($gig->images, true)) > 0)
                                                @php
                                                    $images = json_decode($gig->images, true);
                                                    $firstImage = $images[0];
                                                @endphp
                                                <img src="{{ asset('storage/' . $firstImage) }}" 
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
                                                {{ Str::limit($gig->description, 80) }}
                                            </p>
                                            
                                            <!-- Price & Rating -->
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="fw-bold text-primary">
                                                    ${{ number_format($gig->price, 2) }}
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
@endsection

@push('scripts')
<script>
    function openChatWith(userId) {
        Livewire.dispatch('open-chat-sidebar', { userId: userId });
    }
</script>
@endpush

