<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyView extends Model
{
    use HasFactory; // Opsional, jika Anda menggunakan factory untuk testing

    protected $connection = 'mongodb';
    protected $collection = 'property_views'; // Nama collection yang akan dibuat

    protected $fillable = [
        'property_id', // Akan merujuk ke _id dari UserProperty
        'user_id',     // Opsional, ID pengguna yang melihat (jika login)
        'ip_address',  // Opsional, untuk melacak pengunjung anonim dasar
        'user_agent',  // Opsional, untuk info browser/device
        'viewed_at',   // Timestamp kapan properti dilihat
    ];

    // Penting untuk MongoDB agar field tanggal ditangani sebagai objek Carbon
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Mendapatkan properti yang terkait dengan view ini.
     */
    public function property()
    {
        // Asumsi UserProperty menggunakan _id sebagai primary key
        return $this->belongsTo(UserProperty::class, 'property_id', '_id');
    }

    /**
     * Mendapatkan pengguna (jika ada) yang melakukan view.
     */
    public function user()
    {
        // Asumsi User model Anda menggunakan 'id' sebagai primary key standar Laravel.
        // Jika User model Anda juga di MongoDB dengan '_id', ganti 'id' menjadi '_id'.
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}