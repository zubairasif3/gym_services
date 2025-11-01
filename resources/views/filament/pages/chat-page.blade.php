<x-filament-panels::page>
    <script src="https://cdn.tailwindcss.com"></script>

    <div class="flex flex-col md:flex-row gap-6 h-full">

        <!-- Left panel: Chat Rooms -->
        <div class="w-1/4 p-4 border md:border-r border-gray-200 bg-white dark:bg-gray-900 rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-4">Your Conversations</h3>

            @if($this->chatRooms->count())
                <ul role="list">
                    @foreach ($this->chatRooms as $chatRoom)
                        @php
                            $otherUser = $chatRoom->sender_id === auth()->id()
                                ? \App\Models\User::find($chatRoom->receiver_id)
                                : \App\Models\User::find($chatRoom->sender_id);

                            $isActive = $chatRoom->id == $this->chatRoomId;
                        @endphp

                        <li class="flex py-4 first:pt-0 last:pb-0 border-b last:border-b-0">
                            <a href="?chat_room_id={{ $chatRoom->id }}"
                               class="w-full flex items-center gap-3 p-3 rounded-lg transition hover:bg-gray-50 dark:hover:!bg-gray-700{{ $isActive ? ' bg-primary-500' : '' }}">

                                <div class="">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ substr($otherUser->name, 0, 1) }}&amp;color=FFFFFF&amp;background=09090b" alt="Avatar of test">
                                </div>
                                <div class="ml-3 overflow-hidden {{ $isActive ? 'text-white hover:text-[#000]' : 'text-gray-900 dark:text-white' }}">
                                    <p class="text-sm font-medium">{{ $otherUser->name }}</p>
                                    <p class="truncate text-sm opacity-50">{{ $otherUser->email }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No conversations yet.</p>
            @endif
        </div>

        <!-- Right panel: Chat Messages -->
        <div class="md:w-3/4 w-full p-4 bg-white dark:bg-gray-900 rounded-lg shadow-sm flex flex-col h-[600px]">

            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="text-xl font-semibold">Chat</h3>
                {{-- @if ($this->chatRoomId)
                    <span class="text-sm text-gray-500">Room ID: {{ $this->chatRoomId }}</span>
                @endif --}}
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto mt-4 space-y-4 px-1" id="chat-scroll-container">
                @if ($this->messages->count())
                    @foreach ($this->messages as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] p-3 rounded-lg shadow-sm
                                {{ $message->sender_id === auth()->id() ? ' bg-primary-500 text-white' : 'bg-gray-100 text-gray-800' }}">
                                <p class="text-sm">{{ $message->message }}</p>
                                <div class="text-xs mt-1 text-right opacity-60">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">No messages in this conversation yet.</p>
                @endif
            </div>

            <!-- New message form -->
            @if ($this->chatRoomId)
                <form wire:submit.prevent="sendMessage" class="mt-4 border-t pt-4">
                    <div class="flex items-center gap-2">
                        <textarea wire:model="newMessage"
                                  class="border focus-visible:border-primary-500 focus:ring-0 p-3 resize-none rounded-md w-full dark:!bg-gray-700"
                                  placeholder="Type your message..." rows="2"></textarea>
                        <button type="submit"
                                class=" bg-primary-600 hover: bg-primary-700 text-white px-4 py-2 rounded-md transition">
                            Send
                        </button>
                    </div>
                    @error('newMessage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </form>
            @else
                <div class="mt-4 text-gray-500">Select a conversation to start chatting.</div>
            @endif

        </div>
    </div>

    <!-- Optional: Auto scroll to bottom -->
    <script>
        document.addEventListener("livewire:load", () => {
            const container = document.getElementById('chat-scroll-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
    @php
        $path = request()->path();
        // Check if the path is 'livewire/update'
        if ($path === 'livewire/update') {
            $referer = request()->header('referer');
            if ($referer) {
                $refererParsed = parse_url($referer);
                $path = isset($refererParsed['path']) ? $refererParsed['path'] : 'No path in referer';
            }
        }
    @endphp
    @if($path == 'chat' || $path == '/chat')
        <style>
            .fi-topbar,
            aside {
                display: none !important;
            }
        </style>
    @endif
</x-filament-panels::page>
