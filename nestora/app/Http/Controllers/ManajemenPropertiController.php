<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Property;
 // Tambahkan ini jika ingin return response sederhana

class ManajemenPropertiController extends Controller
{
    /**
     * Menampilkan halaman daftar properti (untuk admin).
     * Route: manajemen-properti.index
     */
    public function index()
    {

        $dataProperty = Property::latest()->paginate(15);
        // Kosongkan dulu atau beri placeholder
        return view('manajemen-properti.index',compact('dataProperty'));
        // return response("Halaman Daftar Properti (Admin)"); // Placeholder
    }

    /**
     * Menampilkan halaman persetujuan iklan properti.
     * Route: manajemen-properti.persetujuan
     */
    public function persetujuan()
    {
        // Kosongkan dulu atau beri placeholder
        return view('manajemen-properti.persetujuan');
        // return response("Halaman Persetujuan Iklan Properti"); // Placeholder
    }

    /**
     * Menampilkan form untuk menambah properti baru.
     * Route: manajemen-properti.create
     */
    public function create()
    {
        // Kosongkan dulu atau beri placeholder
        return view('manajemen-properti.create');
        //  return response("Halaman Form Tambah Properti"); // Placeholder
    }

    /**
     * Menyimpan properti baru ke database.
     * Route: manajemen-properti.store (Method: POST)
     */
    public function store(Request $request)
    {
        // Kosongkan dulu
        // Logika validasi dan penyimpanan ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Simpan belum diimplementasikan.');
        // return response("Proses Simpan Properti (Belum Implementasi)"); // Placeholder
    }

    /**
     * Menampilkan detail properti (Opsional, tapi baik untuk CRUD).
     * Route: manajemen-properti.show
     */
    public function show($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        return view('manajemen-properti.show');
        // return response("Halaman Detail Properti ID: " . $properti); // Placeholder
    }


    /**
     * Menampilkan form untuk mengedit properti.
     * Route: manajemen-properti.edit
     */
    public function edit($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        return view('manajemen-properti.edit');
        //  return response("Halaman Form Edit Properti ID: " . $properti); // Placeholder
    }

    /**
     * Memperbarui data properti di database.
     * Route: manajemen-properti.update (Method: PATCH/PUT)
     */
    public function update(Request $request, $properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        // Logika validasi dan update ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Update belum diimplementasikan.');
        //  return response("Proses Update Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }

    /**
     * Menghapus properti dari database.
     * Route: manajemen-properti.destroy (Method: DELETE)
     */
    public function destroy($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        // Logika delete ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Hapus belum diimplementasikan.');
        // return response("Proses Hapus Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }

     /**
     * Mengubah status persetujuan properti (Contoh action tambahan).
     */
    public function approve(Request $request, $properti)
    {
        // Kosongkan dulu
        return redirect()->route('manajemen-properti.persetujuan')->with('info', 'Fungsi Approve belum diimplementasikan.');
        //  return response("Proses Approve Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }

    /**
     * Mengubah status penolakan properti (Contoh action tambahan).
     */
    public function reject(Request $request, $properti)
    {
        // Kosongkan dulu
        return redirect()->route('manajemen-properti.persetujuan')->with('info', 'Fungsi Reject belum diimplementasikan.');
        // return response("Proses Reject Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }
}