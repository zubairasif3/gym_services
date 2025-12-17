<div>
    <!-- Review Statistics -->
    <div class="reviews-stats mb-4">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="text-center">
                    <h2 class="display-4 fw-bold mb-0">{{ number_format($stats['average'], 1) }}</h2>
                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star{{ $i <= $stats['average'] ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted">{{ $stats['total'] }} {{ $stats['total'] == 1 ? 'review' : 'reviews' }}</p>
                </div>
            </div>
            <div class="col-md-8">
                @foreach($stats['breakdown'] as $rating => $count)
                    <div class="d-flex align-items-center mb-2">
                        <button 
                            wire:click="setFilterRating({{ $rating }})" 
                            class="btn btn-sm {{ $filterRating == $rating ? 'btn-warning' : 'btn-outline-secondary' }} me-2"
                            style="min-width: 70px;">
                            {{ $rating }} <i class="fas fa-star"></i>
                        </button>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                        <span class="ms-2 text-muted" style="min-width: 40px;">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Filters and Sorting -->
    <div class="reviews-controls d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <button wire:click="setSortBy('recent')" 
                    class="btn btn-sm {{ $sortBy == 'recent' ? 'btn-primary' : 'btn-outline-primary' }}">
                Most Recent
            </button>
            <button wire:click="setSortBy('helpful')" 
                    class="btn btn-sm {{ $sortBy == 'helpful' ? 'btn-primary' : 'btn-outline-primary' }}">
                Most Helpful
            </button>
            <button wire:click="setSortBy('rating_high')" 
                    class="btn btn-sm {{ $sortBy == 'rating_high' ? 'btn-primary' : 'btn-outline-primary' }}">
                Highest Rating
            </button>
            <button wire:click="setSortBy('rating_low')" 
                    class="btn btn-sm {{ $sortBy == 'rating_low' ? 'btn-primary' : 'btn-outline-primary' }}">
                Lowest Rating
            </button>
        </div>
        
        @if($filterRating)
            <button wire:click="setFilterRating(null)" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times"></i> Clear Filter
            </button>
        @endif
    </div>
    
    <!-- Reviews List -->
    <div class="reviews-list">
        @forelse($reviews as $review)
            <div class="review-card border rounded p-4 mb-3">
                <div class="d-flex align-items-start">
                    <div class="review-avatar me-3">
                        @if($review->user->avatar_url)
                            <img src="{{ asset('storage/' . $review->user->avatar_url) }}" 
                                 class="rounded-circle" 
                                 width="50" 
                                 height="50" 
                                 alt="{{ $review->user->name }}">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; font-weight: bold;">
                                {{ $review->user->initials }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="review-content flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $review->user->name }} {{ $review->user->surname }}</h6>
                                <div class="text-warning mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-muted small mb-0">
                                    {{ $review->created_at->format('M d, Y') }}
                                    @if($review->is_verified)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle"></i> Verified Purchase
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <p class="review-text mb-3">{{ $review->comment }}</p>
                        
                        <div class="review-actions">
                            <button wire:click="markHelpful({{ $review->id }})" 
                                    class="btn btn-sm btn-outline-secondary">
                                <i class="far fa-thumbs-up"></i> Helpful ({{ $review->helpful_count }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="far fa-comment-slash text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No reviews yet</h5>
                <p class="text-muted">Be the first to review this service!</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @endif
    
    <!-- Load More Button -->
    @if($reviews->hasMorePages())
        <div class="text-center mt-4">
            <button wire:click="loadMore" class="btn btn-primary">
                Load More Reviews
            </button>
        </div>
    @endif
    
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <style>
        .reviews-stats .progress {
            background-color: #e9ecef;
        }
        
        .review-card {
            transition: all 0.3s ease;
        }
        
        .review-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .review-text {
            line-height: 1.6;
            color: #495057;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
    </style>
</div>
