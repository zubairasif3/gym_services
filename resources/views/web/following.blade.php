@extends('web.layouts.app')

@section('title', __('web.following.title'))

@section('content')
<section class="our-dashbord dashbord bgc-f7 pb50">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> {{ __('web.dashboard.navigation') }}</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>{{ __('web.dashboard.dashboard') }}</a></li>
                            <li><a href="{{ route('following') }}" class="active"><span class="flaticon-heart mr10"></span>{{ __('web.dashboard.following') }}</a></li>
                            <li><a href="{{ route('notifications') }}"><span class="flaticon-bell mr10"></span>{{ __('web.dashboard.notifications') }}</a></li>
                            <li><a href="{{ route('messages') }}"><span class="flaticon-chat mr10"></span>{{ __('web.dashboard.messages') }}</a></li>
                            @if(auth()->user()->user_type === 2)
                            <li><a href="{{ route('appointments.index') }}"><span class="flaticon-calendar mr10"></span>{{ __('web.dashboard.my_appointments') }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="row">
                    <!-- Dashboard Title -->
                    <div class="col-lg-12 mb20">
                        <div class="dashboard_title_area">
                            <h2>{{ __('web.following.title') }}</h2>
                            <p class="text">{{ __('web.following.subtitle') }}</p>
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
                                                       class="text-dark text-decoration-none notranslate" translate="no">
                                                        {{ $user->name }} {{ $user->surname }}
                                                    </a>
                                                </h5>
                                                
                                                <!-- Username -->
                                                <p class="text-muted small mb-2 notranslate" translate="no">@<a href="{{ route('professional.profile', $user->username) }}" class="text-muted">{{ $user->username }}</a></p>
                                                
                                                <!-- Stats -->
                                                <div class="d-flex justify-content-center gap-3 mb-3">
                                                    <div>
                                                        <strong>{{ \App\Models\User::followersCount($user->id) }}</strong>
                                                        <small class="text-muted d-block">{{ __('web.following.followers') }}</small>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->services->count() }}</strong>
                                                        <small class="text-muted d-block">{{ __('web.following.services') }}</small>
                                                    </div>
                                                </div>
                                                
                                                <!-- Follow Button -->
                                                @livewire('follow-button', ['user' => $user], key('follow-' . $user->id))
                                                
                                                <!-- Message Button -->
                                                <button class="btn btn-outline-primary btn-sm mt-2 w-100" 
                                                        onclick="openChatWith({{ $user->id }})">
                                                    <i class="far fa-envelope me-1"></i> {{ __('web.following.message') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="far fa-heart text-muted" style="font-size: 4rem;"></i>
                                            <h4 class="mt-3">{{ __('web.following.empty_title') }}</h4>
                                            <p class="text-muted">{{ __('web.following.empty_text') }}</p>
                                            <a href="{{ route('web.services') }}" class="btn btn-primary mt-3">
                                                {{ __('web.following.browse_services') }}
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

