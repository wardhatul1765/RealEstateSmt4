<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Opsional
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory; // Opsional, tambahkan jika pakai factory

    protected $connection = 'mongodb';
    protected $collection = 'propertie'; // Pastikan ini sama dengan nama collectionmu

    public $timestamps = false; // <-- SANGAT PENTING

    protected $fillable = [
        'bathrooms',
        'bedrooms',
        'type',
        'price',
        'furnishing',
        'sizeMin',
        'verified',
        'title',
        'displayAddress',
        'addedOn',
        'view_type',
        'keyword_flags',
    ];

    protected $casts = [
        'furnishing' => 'string',         // Data: "Furnished" (string) -> OK
        'type' => 'string',               // Data: "Apartment" (string) -> OK
        'title' => 'string',              // Data: "Spacious 2 Bedroom Apartment" (string) -> OK
        'displayAddress' => 'string',     // Data: "123 Main St, Dubai" (string) -> OK        // Data: "Furnished" (string) -> OK
        'price' => 'integer',             // Data: 2500000 (integer) -> OK
        'bathrooms' => 'integer',         // Data: 3.0 (float) -> OK, PHP akan handle jadi 3
        'bedrooms' => 'integer',          // Data: 2.0 (float) -> OK, PHP akan handle jadi 2
        'sizeMin' => 'float',             // Data: 1323 (integer) -> OK, bisa di-cast ke float
        'verified' => 'boolean',          // Data: True (boolean Python) -> OK, MongoDB boolean
        'addedOn' => 'datetime',          // Data: "2024-08-14T12:02:53Z" (ISO 8601 string) -> OK, Carbon bisa parse
        'keyword_flags' => 'string',       // Data: None (null di MongoDB) atau ["spacious"] (array) -> OK
        'view_type' => 'string',
        'listing_age_category' => 'integer',
    ];

}