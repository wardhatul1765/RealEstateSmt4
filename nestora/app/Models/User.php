<?php

namespace App\Models;

// Gunakan Contract dan Trait Authenticatable standar Laravel
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

// TAMBAHKAN Contract CanResetPassword
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
// TAMBAHKAN Trait CanResetPassword
use Illuminate\Auth\Passwords\CanResetPassword;

// Gunakan MustVerifyEmail jika perlu verifikasi email
// use Illuminate\Contracts\Auth\MustVerifyEmail;

// Gunakan base Model dari package resmi mongodb/laravel-mongodb
use MongoDB\Laravel\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// Jika pakai Spatie Roles/Permissions: use Spatie\Permission\Traits\HasRoles;

// Implementasikan Contract Authenticatable DAN CanResetPassword
class User extends Model implements
    AuthenticatableContract,
    CanResetPasswordContract // <-- TAMBAHKAN INI
    // , MustVerifyEmail // Aktifkan jika perlu
{
    // Gunakan trait-trait standar Laravel, TERMASUK Authenticatable DAN CanResetPassword
    use HasApiTokens, HasFactory, Notifiable, Authenticatable,
        CanResetPassword; // <-- TAMBAHKAN INI

    // Jika pakai Spatie Roles/Permissions, tambahkan: , HasRoles;

    // Tentukan koneksi database yang digunakan model ini
    protected $connection = 'mongodb';

    // Tentukan nama collection (setara tabel)
    protected $collection = 'admin'; // Pastikan nama collection sesuai

    /**
     * The attributes that are mass assignable.
     * _id akan otomatis ditangani oleh MongoDB.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profile_image', // Tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * Driver MongoDB biasanya menangani tipe data seperti ISODate.
     * 'hashed' tetap relevan untuk Laravel Hash facade.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Driver akan mencoba mengonversi ISODate
        'password' => 'hashed',
        // Cast lain jika perlu
    ];

    // Jika Anda menggunakan Spatie Roles/Permissions, pastikan guardName diset jika perlu
    // protected $guard_name = 'web';

    // Metode dari CanResetPassword trait biasanya tidak perlu di-override
    // seperti getEmailForPasswordReset() dan sendPasswordResetNotification()
    // kecuali Anda perlu kustomisasi khusus.
}
