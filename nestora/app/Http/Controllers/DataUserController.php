<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataUserController extends Controller
{
    //

    public function inputDataMaster()
    {
        return view('data_user.input-data-master');
    }

    public function laporanDataMaster()
    {
        return view('data_user.laporan-data-master');
    }
}
