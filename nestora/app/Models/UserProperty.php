<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProperty extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'user_properties';

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
        'user_id',
        'address',
        'image', // <-- sebagai array dari banyak nama file
    ];

    protected $casts = [
        'image' => 'array', // <--- ini penting!
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}

