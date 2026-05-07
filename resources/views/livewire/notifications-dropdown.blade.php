<div class="notifications-dropdown-content bg-white rounded">
    <!-- Header -->
    <div class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom">
        <h6 class="mb-0 fw-bold">{{ __('notifications.title') }}</h6>
        @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="btn btn-sm btn-link text-primary text-decoration-none p-0">
                {{ __('notifications.mark_all_read') }}
            </button>
        @endif
    </div>
    
    <!-- Notifications List -->
    <div class="notifications-list" style="max-height: 400px; overflow-y: auto;">
        @forelse($notifications as $notification)
            <div class="notification-item p-3 border-bottom {{ !$notification['is_read'] ? 'bg-light' : '' }}" 
                 wire:click="markAsRead({{ $notification['id'] }})"
                 style="cursor: pointer; transition: background-color 0.2s;">
                 
                <div class="d-flex">
                    <!-- Avatar or Icon -->
                    <div class="flex-shrink-0 me-3">
                        @if($notification['related_user'])
                            @if($notification['related_user']['avatar'])
                                <img src="{{ $notification['related_user']['avatar'] }}" 
                                     class="rounded-circle" 
                                     width="40" 
                                     height="40">
                            @else
                                <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-size: 14px;">
                                    {{ $notification['related_user']['initials'] }}
                                </div>
                            @endif
                        @else
                            <div class="notification-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="far fa-bell"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow-1">
                        <div class="notification-content">
                            @if($notification['type'] === 'new_follower')
                                <strong>{{ $notification['related_user']['name'] ?? __('notifications.someone') }}</strong>
                                <span class="text-muted">{{ __('notifications.started_following_you') }}</span>
                            @elseif(($notification['type'] ?? '') === 'new_booking_request' || ($notification['data']['type'] ?? '') === 'new_booking_request')
                                @php
                                    $appointmentId = $notification['data']['appointment_id'] ?? null;
                                    $bookingRequestUrl = $appointmentId ? url('/admin/appointments/' . $appointmentId . '/edit') : route('notifications');
                                    $serviceTitle = $notification['data']['service_title'] ?? '';
                                    $clientName = $notification['data']['client_name'] ?? __('notifications.someone');
                                    $bookingRequestMessage = __('notifications.new_booking_request', [
                                        'service' => $serviceTitle,
                                        'client' => $clientName,
                                    ]);
                                @endphp
                                <a href="{{ $bookingRequestUrl }}" class="text-decoration-none text-dark">
                                    <span>{{ $bookingRequestMessage }}</span>
                                    <span class="d-block small text-primary mt-1">{{ __('notifications.view_appointment') }}</span>
                                </a>
                            @elseif(($notification['type'] ?? '') === 'new_gig_reaction')
                                @php
                                    $reactorName = $notification['related_user']['name'] ?? __('notifications.someone');
                                @endphp
                                <a href="{{ route('professional.preview') }}" class="text-decoration-none text-dark">
                                    <span>{{ __('notifications.reacted_to_service', [
                                        'name' => $reactorName,
                                        'emoji' => $notification['data']['emoji'] ?? '',
                                    ]) }}</span>
                                    <span class="d-block small text-primary mt-1">{{ __('notifications.view_profile') }}</span>
                                </a>
                            @elseif(($notification['type'] ?? '') === 'new_media_reaction')
                                @php
                                    $reactorName = $notification['related_user']['name'] ?? __('notifications.someone');
                                    $mediaId = $notification['related_model_id'] ?? null;
                                    $profileBaseUrl = auth()->user() && auth()->user()->username
                                        ? route('professional.profile', auth()->user()->username)
                                        : route('professional.preview');
                                    $profileUrl = $mediaId ? ($profileBaseUrl . '?media_id=' . $mediaId) : $profileBaseUrl;
                                @endphp
                                <a href="{{ $profileUrl }}" class="text-decoration-none text-dark">
                                    <span>{{ __('notifications.reacted_to_media', [
                                        'name' => $reactorName,
                                        'emoji' => $notification['data']['emoji'] ?? '',
                                    ]) }}</span>
                                    <span class="d-block small text-primary mt-1">{{ __('notifications.view_profile') }}</span>
                                </a>
                            @elseif(($notification['type'] ?? '') === 'new_profile_media' || ($notification['type'] ?? '') === 'new_service')
                                @php
                                    $actorName = $notification['related_user']['name'] ?? __('notifications.someone');
                                    $activityUrl = $notification['data']['url'] ?? route('notifications');
                                    $message = __('notifications.posted_new_content');
                                @endphp
                                <a href="{{ $activityUrl }}" class="text-decoration-none text-dark">
                                    <span><strong>{{ $actorName }}</strong> {{ $message }}</span>
                                    <span class="d-block small text-primary mt-1">{{ __('notifications.view') }}</span>
                                </a>
                            @elseif(($notification['type'] ?? '') === 'new_gig_review')
                                @php
                                    $reviewerName = $notification['related_user']['name'] ?? __('notifications.someone');
                                    $rating = $notification['data']['rating'] ?? null;
                                @endphp
                                <a href="{{ route('professional.preview') }}" class="text-decoration-none text-dark">
                                    <span>
                                        @if($rating)
                                            {{ __('notifications.left_review', ['name' => $reviewerName, 'rating' => $rating]) }}
                                        @else
                                            {{ __('notifications.left_review_no_rating', ['name' => $reviewerName]) }}
                                        @endif
                                    </span>
                                    <span class="d-block small text-primary mt-1">{{ __('notifications.view_profile') }}</span>
                                </a>
                            @elseif(($notification['data']['type'] ?? '') === 'appointment_request_received')
                                <span>{{ __('notifications.appointment_request_received', ['service' => $notification['data']['service_title'] ?? '']) }}</span>
                            @elseif(($notification['data']['type'] ?? '') === 'appointment_confirmed')
                                <span>{{ __('notifications.appointment_confirmed', ['service' => $notification['data']['service_title'] ?? '']) }}</span>
                            @elseif(($notification['data']['type'] ?? '') === 'appointment_reminder')
                                <span>{{ __('notifications.appointment_reminder', ['service' => $notification['data']['service_title'] ?? '']) }}</span>
                            @elseif(($notification['data']['type'] ?? '') === 'appointment_cancelled_by_client')
                                <span>{{ __('notifications.appointment_cancelled_by_client', ['service' => $notification['data']['service_title'] ?? '']) }}</span>
                            @elseif(($notification['data']['type'] ?? '') === 'appointment_cancelled_by_professional')
                                <span>{{ __('notifications.appointment_cancelled_by_professional', ['service' => $notification['data']['service_title'] ?? '']) }}</span>
                            @else
                                <span>{{ $notification['data']['message'] ?? __('notifications.new_notification') }}</span>
                            @endif
                        </div>
                        <div class="notification-time text-muted small mt-1">
                            {{ $notification['created_at'] }}
                        </div>
                    </div>
                    
                    <!-- Unread Indicator -->
                    @if(!$notification['is_read'])
                        <div class="flex-shrink-0 ms-2">
                            <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px; padding: 0;"></span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="far fa-bell fs-1 mb-3 d-block"></i>
                <p>{{ __('notifications.no_notifications_yet') }}</p>
            </div>
        @endforelse
    </div>
    
    <!-- Footer -->
    @if(count($notifications) > 0)
        <div class="dropdown-footer p-3 text-center border-top">
            <a href="{{ route('notifications') }}" class="btn btn-sm btn-link text-decoration-none">
                {{ __('notifications.view_all') }}
            </a>
        </div>
    @endif
    
    <style>
    .notification-item:hover {
        background-color: #f8f9fa !important;
    }

    .notifications-list::-webkit-scrollbar {
        width: 6px;
    }

    .notifications-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notifications-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .notifications-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    </style>
</div>
