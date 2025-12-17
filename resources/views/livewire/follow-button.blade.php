<div class="follow-button-component">
    @auth
        <button wire:click="toggleFollow" 
                class="btn {{ $isFollowing ? 'btn-primary' : 'btn-light' }} shadow-sm position-relative"
                style="border: 1px solid {{ $isFollowing ? '#0d6efd' : '#dee2e6' }}; padding: 0.5rem 1rem; min-width: 120px;"
                wire:loading.attr="disabled">
                
            <div wire:loading.remove wire:target="toggleFollow">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-heart {{ $isFollowing ? 'text-white' : 'text-danger' }}"></i>
                    <div class="text-center">
                        <strong class="{{ $isFollowing ? 'text-white' : 'text-dark' }}">{{ $followersCount }}</strong>
                        @if($isFollowing)
                            <small class="text-white fw-medium ms-1" style="font-size: 0.85rem;">Following</small>
                        @else
                            <small class="text-muted fw-medium ms-1" style="font-size: 0.85rem;">Followers</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <div wire:loading wire:target="toggleFollow" class="d-flex align-items-center justify-content-center">
                <span class="spinner-border spinner-border-sm"></span>
            </div>
        </button>
    @else
        <a href="{{ route('web.login') }}" class="btn btn-light shadow-sm" style="border: 1px solid #dee2e6; padding: 0.5rem 1rem; min-width: 120px;">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-heart text-danger"></i>
                <div class="text-center">
                    <strong class="text-dark">{{ $followersCount }}</strong>
                    <small class="text-muted fw-medium ms-1" style="font-size: 0.85rem;">Followers</small>
                </div>
            </div>
        </a>
    @endauth
    
    <style>
        .follow-button-component .btn {
            transition: all 0.3s ease;
        }
        
        .follow-button-component .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        
        .follow-button-component .btn:active {
            transform: translateY(0);
        }
    </style>
</div>
