<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse; // <-- Pastikan baris ini ada
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Hapus atau comment 'use App\Models\RiwayatPrediksi;' jika tidak digunakan lagi untuk ini
// use App\Models\RiwayatPrediksi;

class PredictionController extends Controller
{
    /**
     * Menampilkan form untuk membuat prediksi baru.
     *
     * @return \Illuminate\View\View
     */
     public function create()
     {
         return view('prediksi.create');
     }

    /**
     * Menyimpan data dari form, memproses prediksi via API, menyimpan riwayat ke MongoDB, dan menampilkan hasil.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse // Type hint ini sekarang seharusnya dikenali
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'bathrooms'            => 'required|numeric|min:0',
            'bedrooms'             => 'required|numeric|min:0',
            'furnishing'           => 'required|integer|in:0,1',
            'sizeMin'              => 'required|numeric|min:0',
            'verified'             => 'required|integer|in:0,1',
            'view_type'            => 'required|numeric',
            'listing_age_category' => 'required|numeric',
            'title_keyword'        => 'required|numeric',
        ]);

        // 2. Kirim data ke API Python
        try {
            $response = Http::post('http://localhost:5000/prediksi/create', $validatedData);

            // 3. Cek respons API
            if ($response->successful()) {
                // Ambil hasil prediksi (AED)
                $predictionResultAED = $response->json('prediction_result') ?? 0;

                // Konversi ke IDR
                $aedToIdrRate = 4560; // Sesuaikan kurs jika perlu
                if (!is_numeric($predictionResultAED)) {
                    Log::warning('Hasil prediksi AED dari API tidak numerik: ' . $predictionResultAED);
                    $predictionResultAED = 0;
                }
                $predictionResultIDR = floatval($predictionResultAED) * $aedToIdrRate;

                // === SIMPAN RIWAYAT PREDIKSI KE MONGODB ===
                try {
                    // Tentukan nama collection MongoDB Anda
                    $collectionName = 'riwayat_prediksi'; // Ganti jika perlu

                    // Siapkan data untuk disimpan
                    $dataToInsert = array_merge(
                        $validatedData, // Semua input yang divalidasi
                        [
                            'hasil_prediksi_aed' => $predictionResultAED,
                            'hasil_prediksi_idr' => $predictionResultIDR,
                            'created_at' => Carbon::now(), // Tambahkan timestamp saat ini
                            'updated_at' => Carbon::now(), // Tambahkan timestamp saat ini
                            // Tambahkan field lain jika perlu, misal user ID
                            // 'user_id' => auth()->id(),
                        ]
                    );

                    // Gunakan ->table() bukan ->collection()
                    DB::connection('mongodb')->table($collectionName)->insert($dataToInsert);

                    // == Jika penyimpanan DB BERHASIL, langsung redirect sukses ==
                    return redirect()->route('prediksi.create')
                                     ->with('prediction_result_aed', $predictionResultAED)
                                     ->with('prediction_result_idr', $predictionResultIDR)
                                     ->withInput();
                    // ==========================================================

                } catch (\Exception $dbExc) {
                    // Tangani jika GAGAL menyimpan ke MongoDB
                    Log::error('Gagal menyimpan riwayat prediksi ke MongoDB: ' . $dbExc->getMessage());

                    // == Redirect dengan pesan error DB ==
                    return redirect()->route('prediksi.create')
                                     ->with('prediction_result_aed', $predictionResultAED) // Tetap kirim hasil prediksi
                                     ->with('prediction_result_idr', $predictionResultIDR)
                                     ->with('error', 'Prediksi berhasil, tetapi GAGAL menyimpan riwayat ke database. Error: ' . $dbExc->getMessage()) // Tambahkan pesan error DB
                                     ->withInput();
                    // ====================================
                }
                // ==========================================

            } else {
                // Tangani jika API error
                $errorMessage = $response->body();
                Log::error('API Prediction Error: ' . $errorMessage . ' Status: ' . $response->status());
                 return redirect()->route('prediksi.create')
                                 ->with('error', 'Gagal menghubungi API prediksi. Status: ' . $response->status() . '. Detail: ' . $errorMessage)
                                 ->withInput();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Tangani jika tidak bisa connect ke API
            Log::error('API Connection Error: ' . $e->getMessage());
            return redirect()->route('prediksi.create')
                             ->with('error', 'Tidak dapat terhubung ke server prediksi. Mohon coba lagi nanti.')
                             ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum lainnya
            Log::error('Prediction Store Error: ' . $e->getMessage());
             return redirect()->route('prediksi.create')
                              ->with('error', 'Terjadi kesalahan tidak terduga saat memproses prediksi.')
                              ->withInput();
        }
    }

    /**
     * Menampilkan halaman riwayat prediksi (dari MongoDB).
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        try {
             // Tentukan nama collection MongoDB Anda
            $collectionName = 'riwayat_prediksi'; // Ganti jika perlu

            // Gunakan ->table() bukan ->collection()
            $riwayat_prediksi_cursor = DB::connection('mongodb')
                                        ->table($collectionName) // Gunakan table()
                                        ->orderBy('created_at', 'desc') // Urutkan terbaru dulu
                                        ->get(); // Ambil semua data (hati-hati jika data sangat besar)


            // Kirim data ke view 'prediksi.history'
            // Pastikan view Anda bisa menangani struktur data dari MongoDB
            return view('prediksi.history', ['riwayat_prediksi' => $riwayat_prediksi_cursor]); // Kirim cursor langsung

        } catch (\Exception $e) {
            Log::error('Gagal mengambil riwayat prediksi dari MongoDB: ' . $e->getMessage());
            // Kembali ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Gagal memuat riwayat prediksi.');
        }
    }

}
