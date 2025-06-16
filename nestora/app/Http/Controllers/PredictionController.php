<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller; // Tidak perlu jika sudah extends Controller dasar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse; // <-- Import untuk response JSON


class PredictionController extends Controller
{
    /**
     * Menampilkan form untuk membuat prediksi baru (UNTUK WEB).
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('prediksi.create');
    }

    /**
     * Menyimpan data dari form web, proses prediksi, simpan riwayat, tampilkan hasil (UNTUK WEB).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // ... (Kode method store Anda yang sudah ada, biarkan saja untuk web) ...
        // 1. Validasi Input Web
        $validatedData = $request->validate([
            'bathrooms'           => 'required|numeric|min:0',
            'bedrooms'            => 'required|numeric|min:0',
            'furnishing'          => 'required|integer|in:0,1,2', // Berbeda dari mobile?
            'sizeMin'             => 'required|numeric|min:0', // Berbeda dari mobile?
            'verified'            => 'required|integer|in:0,1', // Berbeda dari mobile?
            'view_type'           => 'required|numeric',       // Berbeda dari mobile?
            'listing_age_category' => 'required|numeric',       // Berbeda dari mobile?
            'title_keyword'       => 'required|numeric',       // Berbeda dari mobile?
        ]);

        // 2. Kirim data ke API Python (Flask)
        try {
            // Asumsi endpoint Flask sama: 'http://localhost:5000/prediksi/create'
            // Perhatikan: Data yang dikirim ke Flask dari web mungkin berbeda strukturnya
            //             dibanding yang dibutuhkan oleh mobile. Sesuaikan jika perlu.
            $response = Http::post(env('FLASK_PREDICT_URL', 'http://localhost:5000/prediksi/create'), $validatedData);

            // 3. Cek respons API
            if ($response->successful()) {
                $predictionResultAED = $response->json('prediction_result') ?? 0;

                // Konversi ke IDR
                $aedToIdrRate = 4560;
                if (!is_numeric($predictionResultAED)) {
                    Log::warning('Hasil prediksi AED dari API (web) tidak numerik: ' . $predictionResultAED);
                    $predictionResultAED = 0;
                }
                $predictionResultIDR = floatval($predictionResultAED) * $aedToIdrRate;

                // Simpan Riwayat ke MongoDB (jika perlu)
                try {
                    $collectionName = 'riwayat_prediksi';
                    $dataToInsert = array_merge(
                        $validatedData,
                        [
                            'hasil_prediksi_aed' => $predictionResultAED,
                            'hasil_prediksi_idr' => $predictionResultIDR,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            // 'admin_id'    => Auth::id(), // <-- Ambil ID user yang sedang login
                            'admin_name'  => Auth::user()->name,
                            'source' => 'web' // Tandai sumbernya
                        ]
                    );
                    DB::connection('mongodb')->table($collectionName)->insert($dataToInsert);

                    return redirect()->route('prediksi.create')
                        ->with('prediction_result_aed', $predictionResultAED)
                        ->with('prediction_result_idr', $predictionResultIDR)
                        ->withInput();
                } catch (\Exception $dbExc) {
                    Log::error('Gagal menyimpan riwayat prediksi (web) ke MongoDB: ' . $dbExc->getMessage());
                    return redirect()->route('prediksi.create')
                        ->with('prediction_result_aed', $predictionResultAED)
                        ->with('prediction_result_idr', $predictionResultIDR)
                        ->with('error', 'Prediksi berhasil, tetapi GAGAL menyimpan riwayat.')
                        ->withInput();
                }
            } else {
                // Tangani jika API error
                $errorMessage = $response->body();
                Log::error('API Prediction Error (web): ' . $errorMessage . ' Status: ' . $response->status());
                return redirect()->route('prediksi.create')
                    ->with('error', 'Gagal menghubungi API prediksi.')
                    ->withInput();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API Connection Error (web): ' . $e->getMessage());
            return redirect()->route('prediksi.create')
                ->with('error', 'Tidak dapat terhubung ke server prediksi.')
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Prediction Store Error (web): ' . $e->getMessage());
            return redirect()->route('prediksi.create')
                ->with('error', 'Terjadi kesalahan tidak terduga.')
                ->withInput();
        }
    }

    /**
     * Menampilkan halaman riwayat prediksi (UNTUK WEB).
     * @return \Illuminate\View\View
     */
    public function history()
    {
        // ... (Kode method history Anda yang sudah ada, biarkan saja untuk web) ...
        try {
            $collectionName = 'riwayat_prediksi';
            $riwayat_prediksi_cursor = DB::connection('mongodb')
                ->table($collectionName)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('prediksi.history', ['riwayat_prediksi' => $riwayat_prediksi_cursor]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil riwayat prediksi dari MongoDB: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat riwayat prediksi.');
        }
    }

    // ===============================================
    // === METHOD BARU UNTUK API MOBILE (FLUTTER) ===
    // ===============================================
    /**
     * Menerima request dari Flutter, meneruskan ke Flask, dan mengembalikan hasil sebagai JSON.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function predictPriceApi(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'bathrooms'              => 'required|numeric|min:0',
            'bedrooms'               => 'required|numeric|min:0',
            'furnishing'             => 'required|integer|in:0,1,2',
            'sizeMin'                => 'required|numeric|min:0',
            'verified'               => 'required|integer|in:0,1',
            'view_type'              => 'required|numeric',
            'listing_age_category'   => 'required|numeric',
            'title_keyword'          => 'required|numeric',
        ]);

        $flaskApiUrl = env('FLASK_PREDICT_URL', 'http://127.0.0.1:5000/prediksi/create');

        try {
            Log::info('Mengirim data ke Flask dari API Mobile:', $validatedData);
            $response = Http::timeout(15)->post($flaskApiUrl, $validatedData);

            if ($response->successful()) {
                $predictionResult = $response->json();
                Log::info('Menerima respon sukses dari Flask:', $predictionResult);

                if (isset($predictionResult['prediction_result'])) {
                    $predictionResultAED = $predictionResult['prediction_result'];

                    try {
                        DB::connection('mongodb')->table('riwayat_prediksi')->insert(array_merge(
                            $validatedData,
                            [
                                'hasil_prediksi_aed' => $predictionResultAED,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'source' => 'mobile'
                            ]
                        ));
                    } catch (\Exception $dbExc) {
                        Log::error('Gagal menyimpan riwayat prediksi (mobile) ke MongoDB: ' . $dbExc->getMessage());
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Prediksi harga berhasil didapatkan.',
                        'predicted_price' => $predictionResultAED
                    ]);
                } else {
                    Log::error('Struktur response dari Flask tidak valid (mobile)', ['response' => $predictionResult]);
                    return response()->json(['success' => false, 'message' => 'Gagal memproses hasil prediksi.'], 500);
                }
            } else {
                Log::error('Gagal terhubung ke Flask API (mobile)', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menghubungi layanan prediksi.'], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ConnectionException ke Flask API (mobile): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke layanan prediksi.'], 503);
        } catch (\Exception $e) {
            Log::error('Error umum saat prediksi mobile: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal.'], 500);
        }
    }
}
