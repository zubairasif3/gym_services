@extends('web.layouts.app')

@section('title', __('web.messages.title'))

@section('content')
<section class="our-dashbord dashbord bgc-f7 pb50 pt-4">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> {{ __('web.dashboard.navigation') }}</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>{{ __('web.dashboard.dashboard') }}</a></li>
                            <li><a href="{{ route('following') }}"><span class="flaticon-heart mr10"></span>{{ __('web.dashboard.following') }}</a></li>
                            <li><a href="{{ route('notifications') }}"><span class="flaticon-bell mr10"></span>{{ __('web.dashboard.notifications') }}</a></li>
                            <li><a href="{{ route('messages') }}" class="active"><span class="flaticon-chat mr10"></span>{{ __('web.dashboard.messages') }}</a></li>
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
                            <h2>{{ __('web.messages.title') }}</h2>
                            <p class="text">{{ __('web.messages.subtitle') }}</p>
                        </div>
                    </div>
                    
                    <!-- Chat Interface - Use the Chat Sidebar Component -->
                    <div class="col-lg-12">
                        <div class="dashboard_setting_box p-4">
                            <div class="text-center py-5">
                                <i class="far fa-comments text-primary" style="font-size: 5rem;"></i>
                                <h3 class="mt-4 mb-3">{{ __('web.messages.open_chat') }}</h3>
                                <p class="text-muted mb-4">{{ __('web.messages.open_chat_text') }}</p>
                                <button onclick="openChatSidebar()" class="btn btn-primary btn-lg">
                                    <i class="far fa-comments me-2"></i> {{ __('web.messages.open_chat_sidebar') }}
                                </button>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="row mt-5">
                                <div class="col-md-4 text-center">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <i class="far fa-envelope text-info fs-1 mb-3"></i>
                                            <h4>{{ $totalRooms }}</h4>
                                            <p class="text-muted mb-0">{{ __('web.messages.total_conversations') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <i class="fas fa-comment-dots text-success fs-1 mb-3"></i>
                                            <h4>{{ $totalMessages }}</h4>
                                            <p class="text-muted mb-0">{{ __('web.messages.total_messages') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <i class="fas fa-exclamation-circle text-danger fs-1 mb-3"></i>
                                            <h4>{{ $unreadCount }}</h4>
                                            <p class="text-muted mb-0">{{ __('web.messages.unread_messages') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    function openChatSidebar() {
        Livewire.dispatch('open-chat-sidebar');
    }
</script>
@endpush

