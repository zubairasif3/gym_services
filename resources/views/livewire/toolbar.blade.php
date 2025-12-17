<div class="toolbar-wrapper bg-dark border-bottom" wire:poll.30s="loadCounts">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-end py-2">
            
            <!-- Notifications -->
            <div class="col-auto position-relative">
                <button class="toolbar-icon btn btn-link text-white position-relative p-2" 
                        wire:click="toggleNotifications"
                        title="Notifications">
                    <i class="far fa-bell fs-5"></i>
                    @if($notificationsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $notificationsCount > 99 ? '99+' : $notificationsCount }}
                        </span>
                    @endif
                </button>
                
                <!-- Notifications Dropdown -->
                @if($showNotifications)
                    <div class="toolbar-dropdown notifications-dropdown position-absolute end-0 mt-2 shadow-lg" 
                         style="min-width: 350px; max-width: 400px; z-index: 1050;">
                        @livewire('notifications-dropdown')
                    </div>
                @endif
            </div>
            
            <!-- Messages -->
            <div class="col-auto position-relative">
                <button class="toolbar-icon btn btn-link text-white position-relative p-2" 
                        wire:click="toggleMessages"
                        title="Messages">
                    <i class="far fa-envelope fs-5"></i>
                    @if($messagesCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $messagesCount > 99 ? '99+' : $messagesCount }}
                        </span>
                    @endif
                </button>
                
                <!-- Messages Dropdown -->
                @if($showMessages)
                    <div class="toolbar-dropdown messages-dropdown position-absolute end-0 mt-2 shadow-lg" 
                         style="min-width: 350px; max-width: 400px; z-index: 1050;">
                        @livewire('messages-dropdown')
                    </div>
                @endif
            </div>
            
            <!-- Following/Followers -->
            <div class="col-auto">
                <a href="{{ route('following') }}" class="toolbar-icon btn btn-link text-white text-decoration-none p-2" title="Following">
                    <i class="far fa-heart fs-5"></i>
                    <span class="ms-1">{{ $followingCount }}</span>
                </a>
            </div>
            
            <!-- Profile -->
            <div class="col-auto position-relative">
                <button class="toolbar-icon btn btn-link p-1" 
                        wire:click="toggleProfile"
                        title="Profile">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" 
                             class="rounded-circle" 
                             width="36" 
                             height="36"
                             alt="{{ auth()->user()->name }}">
                    @else
                        <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 36px; height: 36px; font-size: 14px; font-weight: bold;">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                </button>
                
                <!-- Profile Dropdown -->
                @if($showProfile)
                    <div class="toolbar-dropdown profile-dropdown position-absolute end-0 mt-2 shadow-lg" 
                         style="min-width: 250px; z-index: 1050;">
                        @livewire('profile-dropdown')
                    </div>
                @endif
            </div>
            
        </div>
    </div>
    
    <style>
    .toolbar-wrapper {
        background-color: #1b1b18 !important;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .toolbar-icon {
        transition: all 0.3s ease;
    }

    .toolbar-icon:hover {
        transform: scale(1.1);
        opacity: 0.8;
    }

    .toolbar-dropdown {
        background: white;
        border-radius: 8px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .badge {
        font-size: 0.65rem;
        padding: 0.25em 0.5em;
    }
    </style>
</div>
