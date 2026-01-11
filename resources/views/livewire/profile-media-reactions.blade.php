<div>
    <div class="reaction-emoji-bar">
        <div class="emoji-bar-container">
            @foreach($this::EMOJIS as $emoji)
                @php
                    $count = $reactions[$emoji] ?? 0;
                    $isActive = isset($userReactions[$emoji]);
                @endphp
                <button 
                    wire:click="reactToMedia({{ $mediaId }}, '{{ $emoji }}')"
                    class="emoji-bar-item {{ $isActive ? 'active' : '' }}"
                    title="{{ $emoji }}">
                    <span class="emoji-icon-bar">{{ $emoji }}</span>
                    @if($count > 0)
                        <span class="emoji-count-bar">{{ $count }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
