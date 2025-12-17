<?php

namespace App\Livewire;

use App\Models\Gig;
use App\Models\GigReview;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ReviewsList extends Component
{
    use WithPagination;
    
    public $gigId;
    public $sortBy = 'recent'; // recent, helpful, rating_high, rating_low
    public $filterRating = null; // 1-5 or null for all
    public $perPage = 10;
    
    protected $queryString = ['sortBy', 'filterRating'];
    
    public function mount($gigId)
    {
        $this->gigId = $gigId;
    }
    
    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }
    
    public function setFilterRating($rating)
    {
        $this->filterRating = $rating === $this->filterRating ? null : $rating;
        $this->resetPage();
    }
    
    public function markHelpful($reviewId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in.');
            return redirect()->route('web.login');
        }
        
        $review = GigReview::findOrFail($reviewId);
        $review->increment('helpful_count');
        
        session()->flash('message', 'Thank you for your feedback!');
    }
    
    public function loadMore()
    {
        $this->perPage += 10;
    }
    
    public function render()
    {
        $gig = Gig::findOrFail($this->gigId);
        
        $query = $gig->reviews()->with('user.profile');
        
        // Apply sorting
        switch ($this->sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->recent();
        }
        
        // Apply rating filter
        if ($this->filterRating) {
            $query->rating($this->filterRating);
        }
        
        $reviews = $query->paginate($this->perPage);
        
        // Calculate statistics
        $stats = [
            'average' => $gig->reviews()->avg('rating') ?? 0,
            'total' => $gig->reviews()->count(),
            'breakdown' => $this->getRatingBreakdown($gig),
        ];
        
        return view('livewire.reviews-list', [
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }
    
    private function getRatingBreakdown($gig)
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $gig->reviews()->where('rating', $i)->count();
        }
        return $breakdown;
    }
}
