<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; // Pastikan ini adalah namespace yang benar untuk MongoDB Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProperty extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'user_properties'; // Nama koleksi di MongoDB

    protected $fillable = [
        'title',
        'description',
        'price',
        'bedrooms',
        'bathrooms',
        'sizeMin',          // Ini adalah areaSqft dari Flutter
        'furnishing',
        'status',
        'user_id',
        'Address',
        'image',            // Array URL gambar
        'propertyType',
        'mainView',         // atau 'surroundingView' sesuai dengan nama field yang Anda gunakan di create()
        'addedOn',// atau 'propertyAge' sesuai dengan nama field yang Anda gunakan di create()
        'propertyLabel',
    ];

    protected $casts = [
        'image' => 'array',         // Ini sudah benar
        'bedrooms' => 'integer',    // Casting eksplisit bisa membantu
        'bathrooms' => 'integer',
        'price' => 'float',         // atau 'double' atau 'decimal:<jumlah_desimal>'
        'sizeMin' => 'float',       // atau 'double'
    ];

    /**
     * Mendapatkan user yang memiliki properti ini.
     */
    public function user()
    {
        // Pastikan App\Models\User adalah model User Anda yang benar
        // dan 'user_id' di UserProperty cocok dengan '_id' (atau primary key) di koleksi User
        return $this->belongsTo(User::class, 'user_id');
    }
}