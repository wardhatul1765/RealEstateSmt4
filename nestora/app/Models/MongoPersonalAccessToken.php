<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasAbilities;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class MongoPersonalAccessToken extends SanctumPersonalAccessToken
{
    use \MongoDB\Laravel\Eloquent\HybridRelations;

    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';

    public $timestamps = false;
}
