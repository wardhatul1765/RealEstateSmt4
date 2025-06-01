<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage; // Import Storage
use Illuminate\Support\Facades\Log;    // Untuk debugging jika perlu

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, JWTSubject
{
    use HasFactory, Notifiable, Authenticatable, CanResetPassword;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',        // Already present
        'profile_image',// Already present
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Accessor for profile_image
    // This ensures that whenever you access $user->profile_image,
    // you get a full URL if a path is stored.
    public function getProfileImageAttribute($value)
    {
        // $value di sini adalah path relatif dari DB, mis: "profile_images/namafile.jpg"
        if ($value && Storage::disk('public')->exists($value)) {
            $filename = basename($value); 
            $url = route('profile.image.serve', ['filename' => $filename]);
            // Log::info('Generated profile image URL via route: ' . $url); // Untuk debug
            return $url;
        }
        // Log::info('Profile image value is null or file does not exist: ' . $value); // Untuk debug
        return null; // Atau URL gambar default jika Anda punya
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id', '_id');
    }
}