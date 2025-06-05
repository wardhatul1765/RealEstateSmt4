<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log; // Untuk debugging jika perlu

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
        'type', // <<< PERBAIKAN 3: Ubah 'propertyType' menjadi 'type' sesuai dengan model Property terbaru
        'sizeMin',
        'furnishing',
        'status',
        'user_id',
        'address',
        'image',
        'propertyType',
        'mainView',
        'listingAgeCategory',
        'propertyLabel',
        'total_views_count', // TAMBAHAN: Untuk menyimpan total view (denormalisasi)
        'bookmarkedBy', // TAMBAHAN: Array untuk menyimpan ID user yang mem-bookmark
    ];

    // protected $guarded = []; // fillable sudah cukup

    protected $casts = [
        'image' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'price' => 'float',
        'sizeMin' => 'float',
        'status' => 'string',
        'total_views_count' => 'integer', // TAMBAHAN
        // 'bookmarkedBy' => 'array', // TAMBAHAN: Casting untuk bookmarkedBy
        'submissionDate' => 'datetime', // Jika Anda punya field ini
        'approvalDate' => 'datetime',   // Jika Anda punya field ini
    ];

    // Relasi ke User (pemilik properti)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Sesuaikan 'id' jika User model PK-nya _id
    }

    // Relasi ke semua log tampilan properti ini
    public function views()
    {
        return $this->hasMany(PropertyView::class, 'property_id', '_id');
    }

    // Accessor untuk menambahkan 'is_favorited_by_user' ke model saat diambil
    protected $appends = ['is_favorited_by_user'];

    public function getIsFavoritedByUserAttribute()
    {
        if (auth('api')->check()) {
            $bookmarkedByArray = $this->bookmarkedBy ?? [];
            if (!is_array($bookmarkedByArray)) { // Safety check
                Log::warning("UserProperty Model: bookmarkedBy is not an array in getIsFavoritedByUserAttribute for property ID {$this->id}. Type: " . gettype($bookmarkedByArray));
                $decoded = json_decode((string)$bookmarkedByArray, true);
                $bookmarkedByArray = is_array($decoded) ? $decoded : [];
            }
            return in_array((string) auth('api')->id(), $bookmarkedByArray);
        }
        return false;
    }

    // Jika Anda memilih untuk TIDAK menggunakan endpoint API statistik terpisah,
    // dan ingin statistik selalu ada saat model UserProperty diambil:
    // Anda bisa uncomment accessor di bawah ini dan tambahkan 'view_statistics' ke $appends
    // protected $appends = ['view_statistics'];
    //
    // public function getViewStatisticsAttribute()
    // {
    //     // Logika agregasi dari getPropertyViewStatistics dipindahkan ke sini
    //     // Ini akan dijalankan setiap kali model diserialisasi ke JSON jika ada di $appends
    //     // Pertimbangkan implikasi performa.
    //     // ... (logika agregasi seperti di APIPropertyController) ...
    //     return [
    //         'daily' => [], // Hasil agregasi harian
    //         'monthly' => [], // Hasil agregasi bulanan
    //     ];
    // }
}