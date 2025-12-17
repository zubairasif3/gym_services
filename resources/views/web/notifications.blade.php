@extends('web.layouts.app')

@section('title', 'Notifications')

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
                            <li><a href="{{ route('following') }}"><span class="flaticon-heart mr10"></span>Following</a></li>
                            <li><a href="{{ route('notifications') }}" class="active"><span class="flaticon-bell mr10"></span>Notifications</a></li>
                            <li><a href="{{ route('messages') }}"><span class="flaticon-chat mr10"></span>Messages</a></li>
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
                                                        <strong>{{ $notification->relatedUser->name ?? 'Someone' }}</strong>
                                                        <span class="text-muted">{{ $notification->data['message'] ?? 'started following you' }}</span>
                                                    @elseif($notification->type === 'new_message')
                                                        <strong>{{ $notification->data['sender_name'] ?? 'Someone' }}</strong>
                                                        <span class="text-muted">sent you a message</span>
                                                        @if(isset($notification->data['message_preview']))
                                                            <div class="text-muted small mt-1">
                                                                "{{ $notification->data['message_preview'] }}"
                                                            </div>
                                                        @endif
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
                                <div class="mt-4">
                                    {{ $notifications->links() }}
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

