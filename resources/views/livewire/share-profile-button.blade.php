<div class="share-button-wrapper position-relative">
    <button 
        wire:click="toggleDropdown" 
        class="btn btn-light d-flex align-items-center gap-2 shadow-sm"
        style="border: 1px solid #dee2e6; padding: 0.5rem 1rem;"
        title="Share this profile">
        <i class="fas fa-share-alt text-primary"></i>
        <span class="text-dark fw-medium">Condividere</span>
    </button>
    
    @if($showDropdown)
        <div class="share-dropdown position-fixed bg-white shadow-lg rounded-3 p-3" 
             id="share-dropdown"
             style="z-index: 10000; min-width: 220px;">
            <div class="d-flex flex-column gap-2">
                <button wire:click="share('facebook')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-primary">
                    <i class="fab fa-facebook text-primary"></i> <span class="notranslate">Facebook</span>
                </button>
                <button wire:click="share('twitter')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-info">
                    <i class="fab fa-twitter text-info"></i> <span class="notranslate">Twitter</span>
                </button>
                <button wire:click="share('whatsapp')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-success">
                    <i class="fab fa-whatsapp text-success"></i> <span class="notranslate">WhatsApp</span>
                </button>
                <button wire:click="share('linkedin')" class="btn btn-sm btn-light w-100 text-start d-flex align-items-center gap-2 hover-primary">
                    <i class="fab fa-linkedin text-primary"></i> <span class="notranslate">LinkedIn</span>
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
                const url = event.detail?.url ?? event.url;
                window.open(url, '_blank', 'width=600,height=400');
            });

            Livewire.on('copy-to-clipboard', (event) => {
                const text = event.detail?.text ?? event.text;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Link copied to clipboard!');
                });
            });
        });
        
        // Update dropdown position when it opens
        function updateDropdownPosition() {
            const dropdown = document.getElementById('share-dropdown');
            const wrapper = document.querySelector('.share-button-wrapper');
            if (dropdown && wrapper && dropdown.offsetParent !== null) {
                const button = wrapper.querySelector('button');
                if (button) {
                    const rect = button.getBoundingClientRect();
                    dropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
                    dropdown.style.right = (window.innerWidth - rect.right) + 'px';
                }
            }
        }
        
        // Update position on Livewire updates (when dropdown state changes)
        document.addEventListener('livewire:update', () => {
            setTimeout(updateDropdownPosition, 10);
        });
        
        // Update position when window resizes or scrolls
        window.addEventListener('resize', updateDropdownPosition);
        window.addEventListener('scroll', updateDropdownPosition, true);
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const wrapper = event.target.closest('.share-button-wrapper');
            const dropdown = document.getElementById('share-dropdown');
            if (!wrapper && dropdown && dropdown.offsetParent !== null) {
                @this.set('showDropdown', false);
            }
        });
    </script>
</div>

