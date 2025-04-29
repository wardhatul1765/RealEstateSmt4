<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // 1. Import Model User

class DataUserController extends Controller
{

    public function index()
    {

        $users = User::orderBy('name')
                     ->paginate(10);

        return view('pengguna.index', compact('users'));
    }
}