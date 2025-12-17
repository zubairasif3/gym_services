@extends('web.layouts.app')

@section('title', 'Following')

@section('content')
<section class="our-dashbord dashbord bgc-f7 pb50">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> Dashboard Navigation</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>Dashboard</a></li>
                            <li><a href="{{ route('following') }}" class="active"><span class="flaticon-heart mr10"></span>Following</a></li>
                            <li><a href="{{ route('notifications') }}"><span class="flaticon-bell mr10"></span>Notifications</a></li>
                            <li><a href="{{ route('messages') }}"><span class="flaticon-chat mr10"></span>Messages</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="row">
                    <!-- Dashboard Title -->
                    <div class="col-lg-12 mb20">
                        <div class="dashboard_title_area">
                            <h2>Following</h2>
                            <p class="text">Manage the professionals you follow</p>
                        </div>
                    </div>
                    
                    <!-- Following List -->
                    <div class="col-lg-12">
                        <div class="dashboard_setting_box">
                            <div class="row">
                                @forelse($following as $user)
                                    <div class="col-md-6 col-lg-4 mb30">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body text-center">
                                                <!-- Avatar -->
                                                @if($user->avatar_url)
                                                    <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                                         class="rounded-circle mb-3" 
                                                         width="80" 
                                                         height="80"
                                                         alt="{{ $user->name }}">
                                                @else
                                                    <div class="avatar-initials bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                                         style="width: 80px; height: 80px; font-size: 28px; font-weight: bold;">
                                                        {{ $user->initials }}
                                                    </div>
                                                @endif
                                                
                                                <!-- Name -->
                                                <h5 class="mb-1">
                                                    <a href="{{ route('professional.profile', $user->username) }}" 
                                                       class="text-dark text-decoration-none">
                                                        {{ $user->name }} {{ $user->surname }}
                                                    </a>
                                                </h5>
                                                
                                                <!-- Username -->
                                                <p class="text-muted small mb-2">@<a href="{{ route('professional.profile', $user->username) }}" class="text-muted">{{ $user->username }}</a></p>
                                                
                                                <!-- Stats -->
                                                <div class="d-flex justify-content-center gap-3 mb-3">
                                                    <div>
                                                        <strong>{{ $user->followers_count }}</strong>
                                                        <small class="text-muted d-block">Followers</small>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->gigs_count }}</strong>
                                                        <small class="text-muted d-block">Services</small>
                                                    </div>
                                                </div>
                                                
                                                <!-- Follow Button -->
                                                @livewire('follow-button', ['user' => $user], key('follow-' . $user->id))
                                                
                                                <!-- Message Button -->
                                                <button class="btn btn-outline-primary btn-sm mt-2 w-100" 
                                                        onclick="openChatWith({{ $user->id }})">
                                                    <i class="far fa-envelope me-1"></i> Message
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="far fa-heart text-muted" style="font-size: 4rem;"></i>
                                            <h4 class="mt-3">You're not following anyone yet</h4>
                                            <p class="text-muted">Discover talented professionals and follow them to stay updated!</p>
                                            <a href="{{ route('web.services') }}" class="btn btn-primary mt-3">
                                                Browse Services
                                            </a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            
                            <!-- Pagination -->
                            @if($following->hasPages())
                                <div class="row mt-4">
                                    <div class="col-12">
                                        {{ $following->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
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

