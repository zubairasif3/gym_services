<div class="messages-dropdown-content bg-white rounded">
    <!-- Header -->
    <div class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom">
        <h6 class="mb-0 fw-bold">Messages</h6>
        <button wire:click="$dispatch('open-chat-sidebar')" class="btn btn-sm btn-link text-primary text-decoration-none p-0">
            <i class="far fa-comments"></i> Open Chat
        </button>
    </div>
    
    <!-- Recent Chats List -->
    <div class="messages-list" style="max-height: 400px; overflow-y: auto;">
        @forelse($recentChats as $chat)
            <div class="message-item p-3 border-bottom" 
                 wire:click="openChat({{ $chat['id'] }})"
                 style="cursor: pointer; transition: background-color 0.2s;">
                 
                <div class="d-flex align-items-center">
                    <!-- Avatar -->
                    <div class="flex-shrink-0 me-3 position-relative">
                        @if($chat['other_user']['avatar'])
                            <img src="{{ $chat['other_user']['avatar'] }}" 
                                 class="rounded-circle" 
                                 width="48" 
                                 height="48">
                        @else
                            <div class="avatar-initials bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px; font-size: 16px;">
                                {{ $chat['other_user']['initials'] }}
                            </div>
                        @endif
                        
                        <!-- Unread Badge -->
                        @if($chat['unread_count'] > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $chat['unread_count'] > 9 ? '9+' : $chat['unread_count'] }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 fw-semibold">{{ $chat['other_user']['name'] }}</h6>
                            @if($chat['last_message'])
                                <span class="text-muted small">{{ $chat['last_message']['time'] }}</span>
                            @endif
                        </div>
                        
                        @if($chat['last_message'])
                            <p class="text-muted small mb-0 text-truncate" 
                               style="max-width: 250px;">
                                {{ $chat['last_message']['text'] }}
                            </p>
                        @else
                            <p class="text-muted small fst-italic mb-0">No messages yet</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="far fa-comments fs-1 mb-3 d-block"></i>
                <p>No conversations yet</p>
                <button wire:click="$dispatch('open-chat-sidebar')" class="btn btn-sm btn-primary mt-2">
                    Start a conversation
                </button>
            </div>
        @endforelse
    </div>
    
    <!-- Footer -->
    @if(count($recentChats) > 0)
        <div class="dropdown-footer p-3 text-center border-top">
            <button wire:click="$dispatch('open-chat-sidebar')" class="btn btn-sm btn-link text-decoration-none">
                View all messages
            </button>
        </div>
    @endif
    
    <style>
    .message-item:hover {
        background-color: #f8f9fa !important;
    }

    .messages-list::-webkit-scrollbar {
        width: 6px;
    }

    .messages-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .messages-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .messages-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    </style>
</div>
