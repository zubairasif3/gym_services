@extends('web.layouts.app')

@section('title', 'Notifications')

@section('content')
<section class="our-dashbord dashbord bgc-f7 pt-4">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> Dashboard Navigation</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>Dashboard</a></li>
                            <li><a href="{{ route('following') }}"><span class="flaticon-heart mr10"></span>Following</a></li>
                            <li><a href="{{ route('notifications') }}" class="active"><span class="flaticon-bell mr10"></span>Notifications</a></li>
                            <li><a href="{{ route('messages') }}"><span class="flaticon-chat mr10"></span>Messages</a></li>
                            @if(auth()->user()->user_type === 2)
                            <li><a href="{{ route('appointments.index') }}"><span class="flaticon-calendar mr10"></span>My Appointments</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="row">
                    <!-- Dashboard Title -->
                    <div class="col-lg-12 mb20">
                        <div class="dashboard_title_area d-flex justify-content-between align-items-center">
                            <div>
                                <h2>Notifications</h2>
                                <p class="text">Stay updated with your activity</p>
                            </div>
                            @if($notifications->where('read_at', null)->count() > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="far fa-check-circle me-1"></i> Mark All as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Notifications List -->
                    <div class="col-lg-12">
                        <div class="dashboard_setting_box">
                            <div class="list-group">
                                @forelse($notifications as $notification)
                                    <div class="list-group-item list-group-item-action {{ is_null($notification->read_at) ? 'bg-light' : '' }}" 
                                         style="cursor: pointer;">
                                        <div class="d-flex align-items-start">
                                            <!-- Avatar/Icon -->
                                            <div class="flex-shrink-0 me-3">
                                                @if($notification->relatedUser)
                                                    @if($notification->relatedUser->avatar_url)
                                                        <img src="{{ asset('storage/' . $notification->relatedUser->avatar_url) }}" 
                                                             class="rounded-circle" 
                                                             width="50" 
                                                             height="50">
                                                    @else
                                                        <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px; font-size: 18px;">
                                                            {{ $notification->relatedUser->initials }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="notification-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="far fa-bell"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-grow-1">
                                                <div class="notification-content mb-1">
                                                    @if($notification->type === 'new_follower')
                                                        <strong class="notranslate" translate="no">{{ $notification->relatedUser->name ?? 'Someone' }}</strong>
                                                        <span class="text-muted">{{ $notification->data['message'] ?? 'started following you' }}</span>
                                                    @elseif($notification->type === 'new_message')
                                                        <strong>{{ $notification->data['sender_name'] ?? 'Someone' }}</strong>
                                                        <span class="text-muted">sent you a message</span>
                                                        @if(isset($notification->data['message_preview']))
                                                            <div class="text-muted small mt-1">
                                                                "{{ $notification->data['message_preview'] }}"
                                                            </div>
                                                        @endif
                                                    @elseif(($notification->type === 'new_booking_request' || ($notification->data['type'] ?? '') === 'new_booking_request'))
                                                        @php
                                                            $appointmentId = $notification->data['appointment_id'] ?? null;
                                                            $bookingRequestUrl = $appointmentId ? url('/admin/appointments/' . $appointmentId . '/edit') : route('notifications');
                                                        @endphp
                                                        <a href="{{ $bookingRequestUrl }}" class="text-decoration-none text-dark d-block">
                                                            <span>{{ $notification->data['message'] ?? 'New booking request' }}</span>
                                                            <span class="d-block small text-primary mt-1">View appointment →</span>
                                                        </a>
                                                    @elseif($notification->type === 'new_gig_reaction')
                                                        @php
                                                            $reactorName = $notification->relatedUser ? trim($notification->relatedUser->name . ' ' . ($notification->relatedUser->surname ?? '')) : 'Someone';
                                                        @endphp
                                                        <a href="{{ route('professional.preview') }}" class="text-decoration-none text-dark d-block">
                                                            <span><strong>{{ $reactorName }}</strong> reacted {{ $notification->data['emoji'] ?? '' }} to your service</span>
                                                            <span class="d-block small text-primary mt-1">View profile →</span>
                                                        </a>
                                                    @elseif($notification->type === 'new_media_reaction')
                                                        @php
                                                            $reactorName = $notification->relatedUser ? trim($notification->relatedUser->name . ' ' . ($notification->relatedUser->surname ?? '')) : 'Someone';
                                                        @endphp
                                                        <a href="{{ route('professional.preview') }}" class="text-decoration-none text-dark d-block">
                                                            <span><strong>{{ $reactorName }}</strong> reacted {{ $notification->data['emoji'] ?? '' }} to your photo/video</span>
                                                            <span class="d-block small text-primary mt-1">View profile →</span>
                                                        </a>
                                                    @elseif($notification->type === 'new_gig_review')
                                                        @php
                                                            $reviewerName = $notification->relatedUser ? trim($notification->relatedUser->name . ' ' . ($notification->relatedUser->surname ?? '')) : 'Someone';
                                                            $rating = $notification->data['rating'] ?? null;
                                                        @endphp
                                                        <a href="{{ route('professional.preview') }}" class="text-decoration-none text-dark d-block">
                                                            <span><strong>{{ $reviewerName }}</strong> left a {{ $rating ? $rating . '-star ' : '' }}review on your service</span>
                                                            <span class="d-block small text-primary mt-1">View profile →</span>
                                                        </a>
                                                    @else
                                                        <span>{{ $notification->data['message'] ?? 'New notification' }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex-shrink-0 ms-3">
                                                @if(is_null($notification->read_at))
                                                    <form action="{{ route('notifications.read', $notification) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as read">
                                                            <i class="far fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="far fa-bell text-muted" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3">No notifications yet</h4>
                                        <p class="text-muted">When you get notifications, they'll show up here</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            <!-- Pagination -->
                            @if($notifications->hasPages())
                                <div class="notifications-pagination-wrapper">
                                    {{ $notifications->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>

    /* Pagination - Custom CSS */
    .notifications-pagination-wrapper {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    .notifications-pagination-wrapper nav {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .notifications-pagination-wrapper nav > div {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
    }
    .notifications-pagination-wrapper nav > div:first-child {
        display: none; /* Mobile: hide by default */
    }
    @media (max-width: 575.98px) {
        .notifications-pagination-wrapper nav > div:first-child {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }
        .notifications-pagination-wrapper nav > div:last-child {
            display: none;
        }
    }
    @media (min-width: 576px) {
        .notifications-pagination-wrapper nav > div:last-child {
            display: flex;
            flex: 1;
            justify-content: space-between;
        }
    }
    .notifications-pagination-wrapper nav .small {
        margin: 0;
        font-size: 0.875rem;
        color: #6c757d;
    }
    .notifications-pagination-wrapper nav .pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
        gap: 0.25rem;
    }
    .notifications-pagination-wrapper nav .pagination .page-item {
        list-style: none;
    }
    .notifications-pagination-wrapper nav .pagination .page-item .page-link {
        display: block;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #00b3f1;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        min-width: 2.5rem;
        text-align: center;
        transition: background-color 0.15s, border-color 0.15s;
    }
    .notifications-pagination-wrapper nav .pagination .page-item .page-link:hover {
        background-color: rgba(0, 179, 241, 0.1);
        border-color: #00b3f1;
    }
    .notifications-pagination-wrapper nav .pagination .page-item.active .page-link {
        background-color: #00b3f1;
        border-color: #00b3f1;
        color: #fff;
    }
    .notifications-pagination-wrapper nav .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
</style>
@endsection

