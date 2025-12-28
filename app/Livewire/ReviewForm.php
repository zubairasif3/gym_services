<?php

namespace App\Livewire;

use App\Models\Gig;
use App\Models\GigReview;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ReviewForm extends Component
{
    public $gigId;
    public $rating = 0;
    public $comment = '';
    public $hasReviewed = false;
    
    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10|max:1000',
    ];
    
    protected $messages = [
        'rating.required' => 'Please select a rating.',
        'rating.min' => 'Rating must be at least 1 star.',
        'rating.max' => 'Rating cannot exceed 5 stars.',
        'comment.required' => 'Please write a comment.',
        'comment.min' => 'Comment must be at least 10 characters.',
        'comment.max' => 'Comment cannot exceed 1000 characters.',
    ];
    
    public function mount($gigId)
    {
        $this->gigId = $gigId;
        $this->checkIfReviewed();
    }
    
    public function checkIfReviewed()
    {
        if (Auth::check()) {
            $this->hasReviewed = GigReview::where('gig_id', $this->gigId)
                ->where('user_id', Auth::id())
                ->exists();
        }
    }
    
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    
    public function submitReview()
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to leave a review.');
            return redirect()->route('web.login');
        }
        
        $this->validate();
        
        // Get the gig to check ownership
        $gig = Gig::find($this->gigId);
        if (!$gig) {
            session()->flash('error', 'Service not found.');
            return;
        }
        
        // Prevent users from reviewing their own services
        if (Auth::id() === $gig->user_id) {
            session()->flash('error', 'You cannot review your own service.');
            return;
        }
        
        // Check if already reviewed
        if ($this->hasReviewed) {
            session()->flash('error', 'You have already reviewed this service.');
            return;
        }
        
        // Create review
        GigReview::create([
            'gig_id' => $this->gigId,
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_verified' => false, // Can be set based on order history
        ]);
        
        // Update gig rating
        $this->updateGigRating();
        
        // Reset form
        $this->reset(['rating', 'comment']);
        $this->hasReviewed = true;
        
        // Dispatch event to refresh reviews list
        $this->dispatch('review-submitted');
        
        session()->flash('message', 'Thank you for your review!');
    }
    
    private function updateGigRating()
    {
        $gig = Gig::find($this->gigId);
        if ($gig) {
            $gig->update([
                'rating' => $gig->reviews()->avg('rating'),
                'ratings_count' => $gig->reviews()->count(),
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.review-form');
    }
}
