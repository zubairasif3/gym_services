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
        
        <div class="d-flex flex-grow-1 overflow-hidden" wire:poll.5s="loadRooms" wire:poll.keep-alive>
            <!-- Conversations List (Left Panel) -->
            <div class="conversations-list border-end" style="width: 300px; overflow-y: auto;">
                <div class="p-2">
                    <h6 class="px-2 py-2 text-muted small fw-bold text-uppercase">Conversations</h6>
                    
                    @forelse($rooms as $room)
                        <div class="conversation-item p-2 rounded mb-1 {{ $activeRoomId == $room['id'] ? 'bg-primary' : '' }}" 
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
                         id="messages-container"
                         wire:poll.2s="loadMessages"
                         wire:poll.keep-alive>
                        @forelse($messages as $item)
                            @if($item['type'] === 'date_separator')
                                <div class="date-separator text-center my-3">
                                    <span class="badge bg-light text-muted px-3 py-1 rounded-pill">
                                        {{ $item['date'] }}
                                    </span>
                                </div>
                            @else
                                <div class="message-bubble mb-3 {{ $item['is_own'] ? 'text-end' : '' }}">
                                    @php
                                        $bd = $item['button_data'] ?? null;
                                        $cardType = $bd['type'] ?? null;
                                        // Confirmation messages that are confirmed: show as plain text bubble (like cancellation), not as card
                                        $isConfirmedPlainMessage = $cardType === 'appointment_confirmation' && !empty($bd['appointment_confirmed']);
                                        $isBookingCard = $bd && !empty($bd['service_title']) && isset($bd['buttons']) && !$isConfirmedPlainMessage;
                                    @endphp
                                    <div class="d-inline-block {{ $isBookingCard ? 'chat-booking-card rounded-3 overflow-hidden shadow-sm border' : '' }} {{ $item['is_own'] ? 'bg-primary text-white' : 'bg-white' }} {{ $isBookingCard ? ($item['is_own'] ? 'border-primary' : 'border-light') : 'rounded p-3' }} shadow-sm" 
                                         style="max-width: 70%; {{ $isBookingCard ? 'min-width: 260px;' : '' }}">
                                         
                                        @if($isBookingCard)
                                            {{-- Enhanced booking/confirmation message card --}}
                                            <div class="p-3 {{ $item['is_own'] ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="rounded-circle d-flex align-items-center justify-content-center {{ $item['is_own'] ? 'bg-white bg-opacity-25' : 'bg-primary bg-opacity-10' }}" style="width: 36px; height: 36px;">
                                                        <i class="far fa-calendar-alt {{ $item['is_own'] ? 'text-white' : 'text-primary' }}"></i>
                                                    </span>
                                                    <span class="small fw-semibold {{ $item['is_own'] ? 'text-white' : 'text-muted' }}">
                                                        @if($cardType === 'new_booking_request')
                                                            New booking request
                                                        @else
                                                            Appointment confirmed
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="mb-3 {{ $item['is_own'] ? 'text-white' : 'text-body' }}">
                                                    <div class="fw-bold mb-1">{{ $bd['service_title'] }}</div>
                                                    <div class="small d-flex align-items-center gap-2 mt-1 opacity-90">
                                                        <i class="far fa-calendar {{ $item['is_own'] ? 'text-white-50' : 'text-muted' }}"></i>
                                                        <span>{{ $bd['appointment_date'] }}</span>
                                                    </div>
                                                    <div class="small d-flex align-items-center gap-2 opacity-90">
                                                        <i class="far fa-clock {{ $item['is_own'] ? 'text-white-50' : 'text-muted' }}"></i>
                                                        <span>{{ $bd['appointment_time'] }}</span>
                                                    </div>
                                                </div>
                                                @if(!empty($bd['appointment_cancelled']))
                                                    <div class="message-buttons mt-2">
                                                        <span class="badge bg-secondary text-white px-2 py-1 rounded"><i class="far fa-ban me-1"></i>Cancelled</span>
                                                    </div>
                                                @elseif(!empty($bd['appointment_confirmed']))
                                                    <div class="message-buttons mt-2">
                                                        <span class="badge bg-success text-white px-2 py-1 rounded"><i class="far fa-check-circle me-1"></i>Confirmed</span>
                                                    </div>
                                                @else
                                                <div class="message-buttons d-flex gap-2 flex-wrap">
                                                    @php
                                                        $isProfessional = auth()->user()->user_type == 3;
                                                        $visibleButtons = $isProfessional
                                                            ? $bd['buttons']
                                                            : collect($bd['buttons'])->filter(fn($b) => in_array($b['action'], ['appointment_request_cancel', 'appointment_cancel']))->values()->all();
                                                    @endphp
                                                    @foreach($visibleButtons as $button)
                                                        @php
                                                            $isConfirm = in_array($button['action'], ['appointment_request_confirm', 'appointment_confirm']);
                                                            $buttonClass = $isConfirm ? 'btn-success' : (match($button['style'] ?? 'primary') {
                                                                'primary' => 'btn-primary',
                                                                'danger' => 'btn-danger',
                                                                'success' => 'btn-success',
                                                                'warning' => 'btn-warning',
                                                                default => 'btn-secondary'
                                                            });
                                                            $isCancelDisabled = ($button['action'] === 'appointment_cancel' && isset($bd['appointment_can_be_cancelled']) && !$bd['appointment_can_be_cancelled']);
                                                            $confirmIcon = $isConfirm ? 'fa-check' : null;
                                                            $cancelIcon = in_array($button['action'], ['appointment_request_cancel', 'appointment_cancel']) ? 'fa-times' : null;
                                                        @endphp
                                                        <button type="button"
                                                            wire:click="handleButtonAction({{ $item['id'] }}, '{{ $button['action'] }}')"
                                                            wire:loading.attr="disabled"
                                                            class="btn btn-sm {{ $buttonClass }} {{ $isCancelDisabled ? 'disabled' : '' }} d-inline-flex align-items-center gap-1"
                                                            @if($isCancelDisabled)
                                                                disabled
                                                                title="Cancellation is only allowed at least 24 hours before the appointment."
                                                            @endif
                                                        >
                                                            <span wire:loading.remove wire:target="handleButtonAction">
                                                                @if($confirmIcon)<i class="far {{ $confirmIcon }}"></i>@endif
                                                                @if($cancelIcon)<i class="far {{ $cancelIcon }}"></i>@endif
                                                                {{ $button['label'] }}
                                                            </span>
                                                            <span wire:loading wire:target="handleButtonAction">...</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            @if($item['message'])
                                                <div class="message-text p-3">{{ $item['message'] }}</div>
                                            @endif
                                            @if(!$isConfirmedPlainMessage && isset($item['button_data']['buttons']) && is_array($item['button_data']['buttons']))
                                                @if(!empty($item['button_data']['appointment_cancelled']))
                                                    <div class="message-buttons px-3 pb-3">
                                                        <span class="badge bg-secondary text-white px-2 py-1 rounded"><i class="far fa-ban me-1"></i>Cancelled</span>
                                                    </div>
                                                @elseif(!empty($item['button_data']['appointment_confirmed']))
                                                    <div class="message-buttons px-3 pb-3">
                                                        <span class="badge bg-success text-white px-2 py-1 rounded"><i class="far fa-check-circle me-1"></i>Confirmed</span>
                                                    </div>
                                                @else
                                                @php
                                                    $isProfessional = auth()->user()->user_type == 3;
                                                    $allBtns = $item['button_data']['buttons'];
                                                    $fallbackButtons = $isProfessional ? $allBtns : array_values(array_filter($allBtns, function ($b) {
                                                        return in_array($b['action'] ?? '', ['appointment_request_cancel', 'appointment_cancel']);
                                                    }));
                                                @endphp
                                                <div class="message-buttons px-3 pb-3 d-flex gap-2 flex-wrap">
                                                    @foreach($fallbackButtons as $button)
                                                        @php
                                                            $isConfirm = in_array($button['action'], ['appointment_request_confirm', 'appointment_confirm']);
                                                            $buttonClass = $isConfirm ? 'btn-success' : (match($button['style'] ?? 'primary') {
                                                                'primary' => 'btn-primary',
                                                                'danger' => 'btn-danger',
                                                                'success' => 'btn-success',
                                                                'warning' => 'btn-warning',
                                                                default => 'btn-secondary'
                                                            });
                                                            $isCancelDisabled = ($button['action'] === 'appointment_cancel' && isset($item['button_data']['appointment_can_be_cancelled']) && !$item['button_data']['appointment_can_be_cancelled']);
                                                        @endphp
                                                        <button type="button"
                                                            wire:click="handleButtonAction({{ $item['id'] }}, '{{ $button['action'] }}')"
                                                            wire:loading.attr="disabled"
                                                            class="btn btn-sm {{ $buttonClass }} {{ $isCancelDisabled ? 'disabled' : '' }}"
                                                            @if($isCancelDisabled)
                                                                disabled
                                                                title="Cancellation is only allowed at least 24 hours before the appointment."
                                                            @endif
                                                        >
                                                            <span wire:loading.remove wire:target="handleButtonAction">{{ $button['label'] }}</span>
                                                            <span wire:loading wire:target="handleButtonAction">...</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endif
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
                                        
                                        <div class="message-time small {{ $item['is_own'] ? 'text-white-50' : 'text-muted' }} mt-1 {{ $isBookingCard ? 'px-3 pb-2' : '' }}">
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
                            
                            <div class="input-group" x-data="{ messageText: '' }">
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
                                       x-on:input="messageText = $event.target.value"
                                       class="form-control" 
                                       placeholder="Type your message..."
                                       maxlength="2000">
                                
                                <!-- Send Button -->
                                <button type="submit" 
                                        class="btn btn-primary"
                                        x-bind:disabled="!messageText || !messageText.trim()">
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
        
        // Handle cancellation modal (client cancelling confirmed appointment)
        Livewire.on('show-cancel-modal', (event) => {
            const appointmentId = event?.appointmentId ?? event?.[0];
            if (!appointmentId) return;
            const reason = prompt('Please provide a cancellation reason (optional):');
            
            if (reason !== null) {
                fetch(`/appointments/${appointmentId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cancellation_reason: reason || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Appointment cancelled successfully!');
                        Livewire.dispatch('cancel-complete');
                    } else {
                        alert('Error: ' + (data.error || 'Failed to cancel appointment'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    });
    
    function checkCancellationTime(appointmentId) {
        // This will be handled by the backend, but we can add a visual check here
        return true;
    }
</script>

<!-- Cancellation Modal -->
<div id="cancel-modal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this appointment?</p>
                <label class="form-label">Cancellation Reason (Optional):</label>
                <textarea id="cancel-reason" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirm-cancel-btn">Cancel Appointment</button>
            </div>
        </div>
    </div>
</div>
    @endif
</div>
