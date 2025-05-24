<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Opsional, tambahkan jika pakai factory
// use Illuminate\Support\Str; // Hapus jika tidak digunakan, Str tidak digunakan dalam cuplikan asli

class Property extends Model
{
    use HasFactory; // Opsional, tambahkan jika menggunakan factory

    protected $connection = 'mongodb';
    protected $collection = 'properties'; // Pastikan ini sama dengan nama collection Anda

    public $timestamps = false; // <-- SANGAT PENTING sesuai catatan Anda

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bathrooms',
        'bedrooms',
        'propertyType',     // Sesuai dengan 'type' sebelumnya
        'price',
        'furnishing',
        'sizeMin',
        'status',           // Sesuai dengan 'verified' sebelumnya
        'title',
        'Address',          // Sesuai dengan 'displayAddress' sebelumnya
        'addedOn',
        'mainView',         // Sesuai dengan 'view_type' sebelumnya
        'propertyLabel',    // Kolom baru
        'user_id',          // Kolom baru
        'image',            // Kolom baru
        'created_at',       // Kolom baru (dikelola manual karena $timestamps = false)
        'updated_at',       // Kolom baru (dikelola manual karena $timestamps = false)
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bathrooms'     => 'integer',       // Data Baru: 3 (integer)
        'bedrooms'      => 'integer',       // Data Baru: 2 (integer)
        'propertyType'  => 'string',        // Data Baru: "Residential for Sale" (string)
        'price'         => 'integer',       // Data Baru: 2500000 (integer)
        'furnishing'    => 'string',        // Data Baru: "NO" (string)
        'sizeMin'       => 'float',         // Data Baru: 1323 (integer), akan di-cast ke float
        'status'        => 'boolean',       // Data Baru: true (boolean)
        'title'         => 'string',        // Data Baru: "great roi i high floor i creek view" (string)
        'Address'       => 'string',        // Data Baru: "Binghatti Canal, Business Bay, Dubai" (string)
        'addedOn'       => 'datetime',      // Data Baru: "2024-08-14T12:02:53Z" (string ISO 8601)
        'mainView'      => 'string',        // Data Baru: null (akan di-cast ke string, atau null jika sumbernya null)
        'propertyLabel' => 'string',        // Data Baru: "" (string)
        'user_id'       => 'string',        // Data Baru: "" (string)
        'image'         => 'string',        // Data Baru: "" (string)
        'created_at'    => 'datetime',      // Data Baru: "2025-05-24T19:12:10.843488" (string mirip ISO 8601)
        'updated_at'    => 'datetime',      // Data Baru: "2025-05-24T19:12:10.844488" (string mirip ISO 8601)
    ];

    // Jika Anda menggunakan Str untuk sesuatu seperti UUID atau slug, Anda mungkin memiliki metode seperti:
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         if (empty($model->id)) { // Asumsikan 'id' adalah primary key Anda dan Anda ingin menjadikannya UUID
    //             $model->id = (string) Str::uuid();
    //         }
    //     });
    // }
}
