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
        return true;
    }

}
