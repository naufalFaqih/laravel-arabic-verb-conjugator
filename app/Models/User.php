<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\SearchHistory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_admin' => 'boolean',
    ];
    
     /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }


    /**
     * Get all search histories for the user.
     */
    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class);
    }

/**
     * Get user's recent activity
     */
    public function getRecentActivityAttribute()
    {
        return $this->searchHistories()
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get user's search count
     */
    public function getSearchCountAttribute()
    {
        return $this->searchHistories()->count();
    }

}