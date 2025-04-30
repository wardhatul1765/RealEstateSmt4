<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Pastikan ini ada
use Illuminate\Http\RedirectResponse; // Pastikan ini ada
// use Illuminate\Support\Facades\Log; // Uncomment jika ingin log error

class PredictionController extends Controller
{
    // ... (method index dan create tetap sama) ...

    /**
     * Menyimpan data dari form, memproses prediksi via API, dan menampilkan hasil.
     */

     public function create()
     {
         return view('prediksi.create');
     }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input (Sudah sesuai dengan form terakhir)
        $validatedData = $request->validate([
            'bathrooms'            => 'required|numeric|min:0',
            'bedrooms'             => 'required|numeric|min:0',
            'furnishing'           => 'required|integer|in:0,1', // Disesuaikan untuk select 0/1
            'sizeMin'              => 'required|numeric|min:0',
            'verified'             => 'required|integer|in:0,1', // Disesuaikan untuk select 0/1
            'view_type'            => 'required|numeric',
            'listing_age_category' => 'required|numeric',
            'title_keyword'        => 'required|numeric',
        ]);

        // 2. Kirim data ke API Python (Flask atau FastAPI) menggunakan Http Client Laravel
        try {
            // Pastikan URL API ini benar (sesuai app.py Anda)
            $response = Http::post('http://localhost:5000/prediksi/create', $validatedData);
            // 3. Cek apakah prediksi berhasil dan proses respons
            if ($response->successful()) {
                // Ambil hasil prediksi (AED)
                $predictionResultAED = $response->json('prediction_result') ?? 0; // Default 0 jika null

                // --- LOGIKA KONVERSI TAMBAHAN ---
                $aedToIdrRate = 4560; // Kurs 1 AED ke IDR (sesuaikan jika perlu)

                // Pastikan hasil AED adalah numerik
                if (!is_numeric($predictionResultAED)) {
                    // Log::warning('Hasil prediksi AED dari API tidak numerik: ' . $predictionResultAED);
                    $predictionResultAED = 0;
                }

                // Hitung nilai IDR
                $predictionResultIDR = floatval($predictionResultAED) * $aedToIdrRate;
                // --- AKHIR LOGIKA KONVERSI ---

                // Kirim KEDUA hasil ke view via session flash
                return redirect()->route('prediksi.create')
                                 ->with('prediction_result_aed', $predictionResultAED) // Kirim nilai AED
                                 ->with('prediction_result_idr', $predictionResultIDR) // Kirim nilai IDR
                                 ->withInput();
            } else {
                // Tangani jika API mengembalikan status error (4xx atau 5xx)
                $errorMessage = $response->body();
                // Log::error('API Prediction Error: ' . $errorMessage . ' Status: ' . $response->status());
                 return redirect()->route('prediksi.create')
                                ->with('error', 'Gagal menghubungi API prediksi. Status: ' . $response->status() . '. Detail: ' . $errorMessage)
                                ->withInput();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Tangani jika tidak bisa terhubung ke API
            // Log::error('API Connection Error: ' . $e->getMessage());
            return redirect()->route('prediksi.create')
                             ->with('error', 'Tidak dapat terhubung ke server prediksi. Mohon coba lagi nanti.')
                             ->withInput();
        } catch (\Exception $e) {
            // Tangani error tidak terduga lainnya
            // Log::error('Prediction Error: ' . $e->getMessage());
             return redirect()->route('prediksi.create')
                              ->with('error', 'Terjadi kesalahan tidak terduga saat memproses prediksi.')
                              ->withInput();
        }
    }

    // ... (method show, edit, update, destroy tetap sama) ...
}