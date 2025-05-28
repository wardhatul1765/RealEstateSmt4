<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol "guard" otentikasi default dan opsi reset password
    | untuk aplikasi Anda. Anda dapat mengubah default ini sesuai kebutuhan.
    | Jika admin panel Anda adalah yang utama menggunakan session, 'web' adalah default yang baik.
    |
    */

    'defaults' => [
        'guard' => 'web', // Tentukan SATU default guard, misal 'web' untuk admin panel
        'passwords' => 'admins', // Default password broker, sesuaikan dengan provider admin
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Di sini Anda mendefinisikan setiap guard otentikasi.
    |
    */

    'guards' => [
        'web' => [ // Guard untuk otentikasi web (admin panel)
            'driver' => 'session',
            'provider' => 'admins', // Menggunakan provider 'admins'
        ],

        'api' => [ // Guard untuk otentikasi API (mobile app)
            'driver' => 'jwt',     // Menggunakan Tymon JWT
            'provider' => 'users',   // Menggunakan provider 'users'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Semua driver otentikasi memiliki user provider. Ini mendefinisikan bagaimana
    | pengguna diambil dari database Anda atau mekanisme penyimpanan lainnya.
    |
    */

    'providers' => [
        'users' => [ // Provider untuk user mobile (dari model User Anda)
            'driver' => 'eloquent', // Atau 'mongodb' jika Anda menggunakan driver mongodb khusus untuk eloquent
            'model' => App\Models\User::class,
        ],

        'admins' => [ // Provider untuk admin
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // GANTI INI jika model Admin Anda berbeda
                                               // atau App\Models\User::class jika admin adalah User dengan role
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk reset password.
    |
    */

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens', // Atau nama tabel yang sesuai untuk admin
            'expire' => 60,
            'throttle' => 60,
        ],
        'users' => [ // Untuk user mobile
            'provider' => 'users',
            'table' => 'password_reset_tokens', // Bisa tabel yang sama atau berbeda
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];