<?php

namespace App\Models; // Namespace untuk model

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model RiwayatPrediksi
 *
 * Mewakili tabel 'riwayat_prediksi' di database.
 */
class RiwayatPrediksi extends Model
{
    // Menggunakan trait HasFactory jika Anda ingin menggunakan model factory untuk testing
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Laravel biasanya bisa menebak nama tabel dari nama model (plural, snake_case),
     * tapi Anda bisa mendefinisikannya secara eksplisit jika perlu.
     *
     * @var string
     */
    // protected $table = 'riwayat_prediksi'; // Uncomment jika nama tabel berbeda

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Ini SANGAT PENTING untuk metode `create()` agar berfungsi.
     * Pastikan semua kolom yang ingin Anda simpan melalui `create()` ada di sini.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'input_bathrooms',
        'input_bedrooms',
        'input_furnishing',
        'input_sizeMin',
        'input_verified',
        'input_view_type',
        'input_listing_age_category',
        'input_title_keyword',
        'hasil_prediksi_aed', // Hasil prediksi dalam AED
        'hasil_prediksi_idr', // Hasil prediksi dalam IDR
        // 'user_id', // Tambahkan ini jika Anda menyimpan siapa yang melakukan prediksi
        // Tambahkan kolom lain yang relevan jika ada
    ];

    /**
     * Atribut yang harus di-cast ke tipe data native.
     * Berguna untuk memastikan tipe data yang benar saat mengambil data.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hasil_prediksi_aed' => 'float', // Contoh cast ke float
        'hasil_prediksi_idr' => 'float', // Contoh cast ke float
        'created_at' => 'datetime', // Otomatis biasanya, tapi bisa didefinisikan
        'updated_at' => 'datetime', // Otomatis biasanya, tapi bisa didefinisikan
    ];

    /**
     * Definisikan relasi jika ada.
     * Contoh: Relasi ke model User (jika Anda menyimpan user_id).
     *
     * public function user()
     * {
     * return $this->belongsTo(User::class);
     * }
     */

}
