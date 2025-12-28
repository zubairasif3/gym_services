<div>
    @if($isOpen)
    <!-- Chat Sidebar Overlay -->
<div class="chat-sidebar-overlay position-fixed top-0 start-0 w-100 h-100" 
     style="background: rgba(0,0,0,0.5); z-index: 10400;"
     wire:click="closeChat">
</div>

<div class="chat-sidebar position-fixed top-0 end-0 h-100 bg-white shadow-lg" 
     style="width: 900px; max-width: 95vw; z-index: 10500; animation: slideIn 0.3s ease;">
     
    <div class="d-flex flex-column h-100">
        <!-- Header -->
        <div class="chat-header p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                @if($activeRoomId && $otherUser)
                    <i class="far fa-comments text-primary me-2"></i>
                    {{ $otherUser['name'] }}
                @else
                    <i class="far fa-comments text-primary me-2"></i>
                    Messages
                @endif
            </h5>
            <button wire:click="closeChat" class="btn btn-link text-dark p-1">
                <i class="fas fa-times fs-4"></i>
            </button>
        </div>
        
        <div class="d-flex flex-grow-1 overflow-hidden">
            <!-- Conversations List (Left Panel) -->
            <div class="conversations-list border-end" style="width: 300px; overflow-y: auto;">
                <div class="p-2">
                    <h6 class="px-2 py-2 text-muted small fw-bold text-uppercase">Conversations</h6>
                    
                    @forelse($rooms as $room)
                        <div class="conversation-item p-2 rounded mb-1 {{ $activeRoomId == $room['id'] ? 'bg-primary bg-opacity-10' : '' }}" 
                             wire:click="selectRoom({{ $room['id'] }})"
                             style="cursor: pointer; transition: all 0.2s;">
                             
                            <div class="d-flex align-items-center">
                                @if($room['other_user']['avatar'])
                                    <img src="{{ $room['other_user']['avatar'] }}" 
                                         class="rounded-circle me-2" 
                                         width="40" 
                                         height="40">
                                @else
                                    <div class="avatar-initials bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ $room['other_user']['initials'] }}
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small">{{ $room['other_user']['name'] }}</div>
                                    @if($room['last_message'])
                                        <div class="text-muted small text-truncate">
                                            {{ $room['last_message']['text'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="far fa-comments fs-1 mb-2 d-block"></i>
                            <p class="small">No conversations</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Active Chat (Right Panel) -->
            <div class="active-chat flex-grow-1 d-flex flex-column">
                @if($activeRoomId)
                    <!-- Chat User Info -->
                    <div class="chat-user-info p-3 border-bottom bg-light">
                        <div class="d-flex align-items-center">
                            @if($otherUser['avatar'])
                                <img src="{{ $otherUser['avatar'] }}" 
                                     class="rounded-circle me-3" 
                                     width="40" 
                                     height="40">
                            @else
                                <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    {{ $otherUser['initials'] }}
                                </div>
                            @endif
                            <h6 class="mb-0 fw-semibold">{{ $otherUser['name'] }}</h6>
                        </div>
                    </div>
                    
                    <!-- Messages Area -->
                    <div class="messages-area flex-grow-1 p-3" 
                         style="overflow-y: auto; background: #f8f9fa;"
                         id="messages-container">
                        @forelse($messages as $item)
                            @if($item['type'] === 'date_separator')
                                <div class="date-separator text-center my-3">
                                    <span class="badge bg-light text-muted px-3 py-1 rounded-pill">
                                        {{ $item['date'] }}
                                    </span>
                                </div>
                            @else
                                <div class="message-bubble mb-3 {{ $item['is_own'] ? 'text-end' : '' }}">
                                    <div class="d-inline-block {{ $item['is_own'] ? 'bg-primary text-white' : 'bg-white' }} rounded p-3 shadow-sm" 
                                         style="max-width: 70%;">
                                         
                                        @if($item['message'])
                                            <div class="message-text">{{ $item['message'] }}</div>
                                        @endif
                                        
                                        @if($item['attachment_path'])
                                            <div class="attachment mt-2">
                                                @if($item['attachment_type'] === 'image')
                                                    <img src="{{ $item['attachment_path'] }}" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 300px; cursor: pointer;"
                                                         onclick="window.open('{{ $item['attachment_path'] }}', '_blank')"
                                                         alt="Attachment">
                                                @elseif($item['attachment_type'] === 'video')
                                                    <video controls class="img-fluid rounded" style="max-height: 300px;">
                                                        <source src="{{ $item['attachment_path'] }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <a href="{{ $item['attachment_path'] }}" 
                                                       target="_blank" 
                                                       download="{{ $item['attachment_name'] ?? 'attachment' }}"
                                                       class="d-inline-flex align-items-center {{ $item['is_own'] ? 'text-white' : 'text-primary' }} text-decoration-none">
                                                        <i class="far fa-file me-2"></i>
                                                        <span>{{ $item['attachment_name'] ?? 'Download Attachment' }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="message-time small {{ $item['is_own'] ? 'text-white-50' : 'text-muted' }} mt-1">
                                            {{ $item['created_at'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="far fa-comment-dots fs-1 mb-2 d-block"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Message Input -->
                    <div class="message-input p-3 border-top bg-white">
                        <form wire:submit.prevent="sendMessage">
                            <!-- Emoji Picker -->
                            @if($showEmojiPicker)
                                <div class="emoji-picker bg-light border rounded p-2 mb-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach(['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎','🔝', '👍', '👎', '👏', '🙌', '👐', '🤝', '🙏', '💪', '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '✨', '⭐', '🌟', '💫', '🔥', '💯', '✅', '❌'] as $emoji)
                                        <button type="button" 
                                                wire:click="addEmoji('{{ $emoji }}')" 
                                                class="btn btn-sm btn-light m-1">
                                            {{ $emoji }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="input-group">
                                <!-- Emoji Toggle -->
                                <button type="button" 
                                        wire:click="toggleEmojiPicker" 
                                        class="btn btn-outline-secondary">
                                    😀
                                </button>
                                
                                <!-- File Upload -->
                                <label class="btn btn-outline-secondary mb-0 d-flex align-items-center" style="cursor: pointer;">
                                    <i class="far fa-paperclip"></i>
                                    <input type="file" 
                                           wire:model="attachment" 
                                           class="d-none"
                                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
                                </label>
                                
                                <!-- Message Input -->
                                <input type="text" 
                                       wire:model="message" 
                                       class="form-control" 
                                       placeholder="Type your message..."
                                       maxlength="2000">
                                
                                <!-- Send Button -->
                                <button type="submit" 
                                        class="btn btn-primary"
                                        {{ !$message && !$attachment ? 'disabled' : '' }}>
                                    <i class="far fa-paper-plane"></i>
                                </button>
                            </div>
                            
                            <!-- Attachment Preview -->
                            @if($attachment)
                                <div class="mt-2 d-flex align-items-center">
                                    <i class="far fa-file text-primary me-2"></i>
                                    <small class="text-muted">{{ $attachment->getClientOriginalName() }}</small>
                                    <button type="button" wire:click="$set('attachment', null)" class="btn btn-sm btn-link text-danger ms-2">
                                        Remove
                                    </button>
                                </div>
                            @endif
                            
                            @error('message') <span class="text-danger small">{{ $message }}</span> @enderror
                            @error('attachment') <span class="text-danger small">{{ $message }}</span> @enderror
                        </form>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="far fa-comments fs-1 mb-3 d-block"></i>
                            <p>Select a conversation to start chatting</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Hide main page scrollbar when chat is open */
body.chat-open {
    overflow: hidden !important;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}

.conversation-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.messages-area::-webkit-scrollbar,
.conversations-list::-webkit-scrollbar {
    width: 6px;
}

.messages-area::-webkit-scrollbar-track,
.conversations-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.messages-area::-webkit-scrollbar-thumb,
.conversations-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.messages-area::-webkit-scrollbar-thumb:hover,
.conversations-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.message-bubble {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.date-separator {
    position: relative;
}

.date-separator::before,
.date-separator::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background: #dee2e6;
}

.date-separator::before {
    left: 0;
}

.date-separator::after {
    right: 0;
}
</style>

<script>
    // Hide main page scrollbar when chat opens, show when closes
    document.addEventListener('livewire:init', function () {
        Livewire.on('chat-opened', () => {
            document.body.classList.add('chat-open');
        });
        
        Livewire.on('chat-closed', () => {
            document.body.classList.remove('chat-open');
        });
    });
    
    // Auto-scroll to bottom when new messages arrive
    document.addEventListener('livewire:load', function () {
        Livewire.on('message-sent', () => {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        });
        
        // Auto-scroll on room selection
        Livewire.on('message-read', () => {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        });
    });
</script>
    @endif
</div>
