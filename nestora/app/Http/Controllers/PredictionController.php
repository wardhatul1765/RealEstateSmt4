<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Tambahkan use statement lain jika diperlukan (misal untuk memanggil model/service prediksi)
// use App\Services\PredictionService; // Contoh

class PredictionController extends Controller
{
    /**
     * Menampilkan daftar hasil prediksi (jika ada halamannya).
     * Jika tidak ada halaman daftar, method ini bisa dikosongkan atau return view lain.
     */
    public function index()
    {
        // Contoh: Jika ada halaman daftar prediksi
        // $hasilPrediksi = ... // Ambil data dari database/model
        // return view('prediksi.index', compact('hasilPrediksi'));

        // Atau jika belum ada halaman index:
         return "Halaman Daftar Prediksi (Belum dibuat)"; // Placeholder
         // return view('predict'); // Jika Anda memang punya view ini untuk index
    }

    /**
     * Menampilkan form untuk membuat prediksi baru. (WAJIB ADA)
     */
    public function create() // <--- METHOD YANG HILANG
    {
        // Mengembalikan view yang berisi form prediksi
        return view('prediksi.create'); // Pastikan view ini benar (resources/views/prediksi/create.blade.php)
    }


    /**
     * Menyimpan data dari form, memproses prediksi, dan menampilkan hasil.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Sangat Penting!)
        $validatedData = $request->validate([
            'bathrooms' => 'required|numeric|min:0',
            'bedrooms' => 'required|numeric|min:0',
            'furnishing' => 'required|integer|min:0|max:1', // integer 0 atau 1
            'sizeMin' => 'required|numeric|min:0',
        ]);

        // 2. Siapkan data untuk dikirim ke model prediksi
        // $inputForModel = [
        //     $validatedData['bathrooms'],
        //     $validatedData['bedrooms'],
        //     $validatedData['furnishing'],
        //     $validatedData['sizeMin']
        // ];

        // 3. Panggil service/model prediksi Anda (Contoh)
        // $predictionService = new PredictionService();
        // $hasil = $predictionService->predict($inputForModel);
        $hasil = "Contoh Hasil Prediksi"; // <<< Ganti dengan logika prediksi Anda

        // 4. Redirect kembali ke form dengan membawa hasil
        return redirect()->route('prediksi.create') // Redirect ke halaman form lagi
                         ->with('prediction_result', $hasil) // Kirim hasil via session flash
                         ->withInput(); // Bawa kembali input sebelumnya (jika perlu)

        // Atau jika ada error saat prediksi:
        // return redirect()->route('prediksi.create')
        //                  ->with('error', 'Pesan error prediksi...')
        //                  ->withInput();
    }

    /**
     * Display the specified resource. (Opsional)
     */
    public function show(string $id)
    {
        // Tampilkan detail satu hasil prediksi (jika perlu)
    }

    /**
     * Show the form for editing the specified resource. (Opsional)
     */
     // public function edit(string $id)
     // {
         // Menampilkan form edit (jika perlu)
     // }


    /**
     * Update the specified resource in storage. (Opsional)
     */
    public function update(Request $request, string $id)
    {
        // Memproses update hasil prediksi (jika perlu)
    }

    /**
     * Remove the specified resource from storage. (Opsional)
     */
    public function destroy(string $id)
    {
        // Menghapus hasil prediksi (jika perlu)
    }
}