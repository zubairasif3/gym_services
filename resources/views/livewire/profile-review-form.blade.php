<div>
    @if($hasReviewed)
        <div class="alert alert-info">
            <i class="fas fa-check-circle me-2"></i>
            You have already reviewed this professional. Thank you for your feedback!
        </div>
    @else
        <div class="review-form-card border rounded p-4 bg-light">
            <h5 class="mb-4">Leave a Review</h5>
            
            <form wire:submit.prevent="submitReview">
                <!-- Rating Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Your Rating *</label>
                    <div class="rating-input">
                        @for($i = 1; $i <= 5; $i++)
                            <button 
                                type="button"
                                wire:click="setRating({{ $i }})"
                                class="btn btn-link p-0 me-1"
                                style="font-size: 2rem; text-decoration: none;">
                                <i class="fas fa-star{{ $i <= $rating ? ' text-warning' : ' text-muted' }}"></i>
                            </button>
                        @endfor
                    </div>
                    @error('rating')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Comment Textarea -->
                <div class="mb-4">
                    <label for="comment" class="form-label fw-bold">Your Review *</label>
                    <textarea 
                        wire:model="comment"
                        id="comment"
                        class="form-control @error('comment') is-invalid @enderror"
                        rows="5"
                        placeholder="Share your experience with this professional... (minimum 10 characters)"
                        maxlength="1000"></textarea>
                    <div class="d-flex justify-content-between mt-1">
                        @error('comment')
                            <div class="text-danger small">{{ $message }}</div>
                        @else
                            <div class="text-muted small">Minimum 10 characters</div>
                        @enderror
                        <div class="text-muted small">{{ strlen($comment) }}/1000</div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-flex gap-2">
                    <button 
                        type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="submitReview">
                        <span wire:loading.remove wire:target="submitReview">
                            <i class="far fa-paper-plane me-1"></i> Submit Review
                        </span>
                        <span wire:loading wire:target="submitReview">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Submitting...
                        </span>
                    </button>
                    
                    @if($rating > 0 || strlen($comment) > 0)
                        <button 
                            type="button"
                            wire:click="$refresh"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    @endif
                </div>
            </form>
            
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif
    
    <style>
        .rating-input .btn:hover i {
            transform: scale(1.2);
            transition: transform 0.2s ease;
        }
        
        .rating-input .text-warning {
            color: #ffc107 !important;
        }
        
        .review-form-card {
            transition: box-shadow 0.3s ease;
        }
        
        .review-form-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        textarea.form-control {
            resize: vertical;
        }
    </style>
</div>
