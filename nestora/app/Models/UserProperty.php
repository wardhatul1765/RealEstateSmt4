<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProperty extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'user_properties'; // khusus properti dari user

    protected $fillable = [
        'title',
        'description',
        'price',
        'bedrooms',
        'bathrooms',
        'sizeMin',
        'furnishing',
        'verified',
        'status',
        'user_id', // <--- ini penting untuk relasi user
        'address',
        'image', // filename utama
        'additional_images', // array filename tambahan
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
