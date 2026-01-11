<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ProfileReview;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfileReviewForm extends Component
{
    public $profileUserId;
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
    
    public function mount($profileUserId)
    {
        $this->profileUserId = $profileUserId;
        $this->checkIfReviewed();
    }
    
    public function checkIfReviewed()
    {
        if (Auth::check()) {
            $this->hasReviewed = ProfileReview::where('profile_user_id', $this->profileUserId)
                ->where('reviewer_id', Auth::id())
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
        
        // Prevent users from reviewing themselves
        if (Auth::id() === $this->profileUserId) {
            session()->flash('error', 'You cannot review your own profile.');
            return;
        }
        
        // Check if already reviewed
        if ($this->hasReviewed) {
            session()->flash('error', 'You have already reviewed this profile.');
            return;
        }
        
        // Create review
        ProfileReview::create([
            'profile_user_id' => $this->profileUserId,
            'reviewer_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_verified' => false,
        ]);
        
        // Reset form
        $this->reset(['rating', 'comment']);
        $this->hasReviewed = true;
        
        // Dispatch event to refresh reviews list
        $this->dispatch('profile-review-submitted');
        
        session()->flash('message', 'Thank you for your review!');
        
        // Redirect to refresh the page and show new review
        return redirect()->to(request()->url());
    }
    
    public function render()
    {
        return view('livewire.profile-review-form');
    }
}
