<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Opsional

class Property extends Model
{
    use HasFactory; // Opsional, tambahkan jika pakai factory

    protected $connection = 'mongodb';
    protected $collection = 'property'; // Pastikan ini sama dengan nama collectionmu

    public $timestamps = false; // <-- SANGAT PENTING

    protected $fillable = [
        '_id', // Jika _id juga bagian dari data JSON yang diimpor dan mau di-manage Eloquent
        'bathrooms',
        'bedrooms',
        'type',
        'price',
        'furnishing',
        'sizeMin',
        'verified',
        'title',
        'addedOn',
        'view_type',
        'keyword_flags',
    ];

    protected $casts = [
        'price' => 'integer',
        'bathrooms' => 'integer',
        'bedrooms' => 'integer',
        'sizeMin' => 'float', // Atau integer
        'verified' => 'boolean', // JSON-mu sudah boolean (true/false), jadi ini akan bekerja
        'addedOn' => 'datetime', // JSON-mu string ISO8601, ini akan di-cast ke Carbon
        // 'furnishing' perlu penanganan khusus jika masih "YES"/"NO" di JSON dan mau jadi boolean
        // Jika sudah boolean di JSON, maka 'furnishing' => 'boolean' cukup.
        // Jika masih string "YES"/"NO" di JSON dan DB juga string: tidak perlu cast.
        // Jika masih string "YES"/"NO" di JSON tapi mau boolean di Eloquent: gunakan accessor.
        'keyword_flags' => 'array', // Jika ini adalah array
    ];

    // Jika 'furnishing' di JSON masih "YES"/"NO" dan kamu mau jadi boolean di Laravel:
    public function getFurnishingAttribute($value)
    {
        if (is_string($value)) {
            return strtoupper($value) === 'YES';
        }
        return (bool) $value; // fallback jika sudah boolean atau tipe lain
    }

    // Jika kamu ingin menyimpan 'furnishing' sebagai boolean di DB
    // tapi menerima input "YES"/"NO" dari form, kamu bisa pakai mutator:
    // public function setFurnishingAttribute($value)
    // {
    //     if (is_string($value)) {
    //         $this->attributes['furnishing'] = (strtoupper($value) === 'YES');
    //     } else {
    //         $this->attributes['furnishing'] = (bool) $value;
    //     }
    // }
}