<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataMasterController extends Controller
{
    public function inputDataMaster()
    {
        return view('data_master.input-data-master');
    }

    public function laporanDataMaster()
    {
        return view('data_master.laporan-data-master');
    }
}
