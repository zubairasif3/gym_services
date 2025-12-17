<div class="share-button-wrapper position-relative">
    <button 
        wire:click="toggleDropdown" 
        class="btn btn-light d-flex align-items-center gap-2 shadow-sm"
        style="border: 1px solid #dee2e6; padding: 0.5rem 1rem;"
        title="Share this service">
        <i class="fas fa-share-alt text-primary"></i>
        <span class="text-dark fw-medium">Condividere</span>
    </button>
    
    @if($showDropdown)
        <div class="share-dropdown position-absolute bg-white shadow-lg rounded-3 p-3" 
             style="top: 100%; right: 0; z-index: 1000; min-width: 220px; margin-top: 8px;">
            <div class="d-flex flex-column gap-2">
                <button wire:click="share('facebook')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-primary">
                    <i class="fab fa-facebook text-primary"></i> <span>Facebook</span>
                </button>
                <button wire:click="share('twitter')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-info">
                    <i class="fab fa-twitter text-info"></i> <span>Twitter</span>
                </button>
                <button wire:click="share('whatsapp')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-success">
                    <i class="fab fa-whatsapp text-success"></i> <span>WhatsApp</span>
                </button>
                <button wire:click="share('linkedin')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-primary">
                    <i class="fab fa-linkedin text-primary"></i> <span>LinkedIn</span>
                </button>
                <hr class="my-2">
                <button wire:click="copyLink" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-dark">
                    <i class="far fa-copy text-dark"></i> <span>Copy Link</span>
                </button>
            </div>
        </div>
    @endif
    
    <style>
        .share-button-wrapper {
            display: inline-block;
        }
        
        .share-dropdown {
            animation: fadeIn 0.2s ease-in-out;
            border: 1px solid #dee2e6;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        
        .share-dropdown .btn:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }
    </style>
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-share-url', (event) => {
                window.open(event.url, '_blank', 'width=600,height=400');
            });
            
            Livewire.on('copy-to-clipboard', (event) => {
                navigator.clipboard.writeText(event.text).then(() => {
                    // Show a brief success message
                    alert('Link copied to clipboard!');
                });
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const wrapper = event.target.closest('.share-button-wrapper');
            if (!wrapper) {
                @this.set('showDropdown', false);
            }
        });
    </script>
</div>
