<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasAbilities;

class MongoPersonalAccessToken extends Model implements HasAbilities
{
    protected $connection = 'mongodb'; // Gunakan koneksi MongoDB
    protected $collection = 'personal_access_tokens'; // Nama koleksi di MongoDB

    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
    ];

    public function tokenable()
    {
        return $this->morphTo();
    }

    public function can($ability)
    {
        return in_array($ability, $this->abilities ?? []);
    }

    public function cant($ability)
    {
        return !$this->can($ability);
    }

    /**
     * Temukan token berdasarkan nilai token.
     *
     * @param string $token
     * @return self|null
     */
    public static function findToken($token)
    {
        return self::where('token', hash('sha256', $token))->first();
    }
}
