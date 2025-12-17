<div>
    <button 
        wire:click="toggleSave" 
        class="btn {{ $isSaved ? 'btn-primary' : 'btn-light' }} d-flex align-items-center gap-2 shadow-sm"
        style="border: 1px solid {{ $isSaved ? '#0d6efd' : '#dee2e6' }}; padding: 0.5rem 1rem;"
        title="{{ $isSaved ? 'Remove from saved' : 'Save this service' }}">
        <i class="fas {{ $isSaved ? 'fa-heart' : 'fa-heart' }} {{ $isSaved ? 'text-white' : 'text-danger' }}"></i>
        <span class="{{ $isSaved ? 'text-white' : 'text-dark' }} fw-medium">{{ $isSaved ? 'Saved' : 'Salva' }}</span>
    </button>
    
    <style>
        .btn {
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        
        .btn i {
            font-size: 1rem;
        }
    </style>
</div>
