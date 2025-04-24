<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class penggunacontroller extends Controller
{
    public function index(){
        return view('pengguna.pengguna');
    }
}
