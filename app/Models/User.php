<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'business_name',
        'email',
        'password',
        'avatar_url',
        'default_payment_method',
        'stripe_customer_id',
        'user_type',
        'followers_count',
        'following_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get all profile media for this user
     */
    public function profileMedia()
    {
        return $this->hasMany(ProfileMedia::class)->ordered();
    }

    /**
     * Get active profile media for this user
     */
    public function activeProfileMedia()
    {
        return $this->hasMany(ProfileMedia::class)->active()->ordered();
    }

    // For checking if the user is a provider
    public function isProvider()
    {
        return $this->profile && $this->profile->is_provider;
    }

    public function getUserTypeLabelAttribute()
    {
        $types = [
            1 => 'Admin',
            2 => 'Customer',
            3 => 'Professional',
        ];

        return $types[$this->user_type] ?? 'Unknown';
    }
    public function gigs()
    {
        return $this->hasMany(Gig::class);
    }

    /**
     * Get the reviews written by this user
     */
    public function gigReviews()
    {
        return $this->hasMany(GigReview::class);
    }

    /**
     * Get the gigs saved by this user
     */
    public function savedGigs()
    {
        return $this->belongsToMany(Gig::class, 'gig_saves')
            ->withTimestamps();
    }

    /**
     * Get all saves by this user
     */
    public function gigSaves()
    {
        return $this->hasMany(GigSave::class);
    }

    /**
     * Get all shares by this user
     */
    public function gigShares()
    {
        return $this->hasMany(GigShare::class);
    }

    public function userSubcategories()
    {
        return $this->hasMany(UserSubcategory::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'user_subcategories')
                    ->withPivot('priority')
                    ->withTimestamps();
    }

    // Followers & Following Relationships
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    // Notifications Relationships
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    // Chat Relationships
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    // Helper Methods
    public function getInitialsAttribute()
    {
        $firstInitial = $this->name ? strtoupper(substr($this->name, 0, 1)) : '';
        $lastInitial = $this->surname ? strtoupper(substr($this->surname, 0, 1)) : '';
        return $firstInitial . $lastInitial;
    }

    public function isProfessional()
    {
        return $this->user_type === 3;
    }

    public function isCustomer()
    {
        return $this->user_type === 2;
    }

    public function isAdmin()
    {
        return $this->user_type === 1;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        // Send different notifications based on user type
        if ($this->user_type == 3) {
            // Professional/Seller
            $this->notify(new \App\Notifications\VerifyEmailProfessional());
        } else {
            // Customer (user_type == 2)
            $this->notify(new \App\Notifications\VerifyEmailCustomer());
        }
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? ('/storage/'. $this->avatar_url) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Only Admin (1) and Professional (3) can access the admin panel
        // Customer (2) cannot access
        return in_array($this->user_type, [1, 3]);
    }

}
