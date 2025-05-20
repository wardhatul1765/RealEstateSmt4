<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

use MongoDB\Laravel\Eloquent\Model; // pastikan benar sesuai package yang Anda pakai

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, JWTSubject
{
    use HasFactory, Notifiable, Authenticatable, CanResetPassword;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey(); // biasakan gunakan getKey(), ini mengembalikan _id untuk MongoDB
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
