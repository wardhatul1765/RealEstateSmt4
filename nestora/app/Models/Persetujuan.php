<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persetujuan extends Model
{
    use HasFactory;


    protected $connection = 'mongodb';
    protected $collection = 'Persetujuan'; // Pastikan ini sama dengan nama collectionmu

    public $timestamps = false;
}
