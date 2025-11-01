<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements FilamentUser, HasAvatar
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
            2 => 'Buyer',
            3 => 'Seller',
        ];

        return $types[$this->user_type] ?? 'Unknown';
    }
    public function gigs()
    {
        return $this->hasMany(Gig::class);
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
