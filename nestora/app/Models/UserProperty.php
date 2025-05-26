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
        'status',
        'user_id',
        'address',          // <<< PERBAIKAN 1: Ubah menjadi 'address' (huruf kecil)
        'image',
        'propertyType',
        'mainView',
        'listingAgeCategory', // <<< PERBAIKAN 2: Ubah 'addedOn' menjadi 'listingAgeCategory'
        'propertyLabel',
    ];

    protected $guarded = ['id']; // Lebih aman mengosongkan ini jika semua field sudah di $fillable

    protected $casts = [
        'image' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'price' => 'float',
        'sizeMin' => 'float',
        'status' => 'string', // Ini sudah benar
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}