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
    public function predictPriceApi(Request $request): JsonResponse // Mengembalikan JsonResponse
    {
        // 1. Validasi Input dari Flutter
        //    Sesuaikan dengan data yang dikirim dari Flutter
        $validatedData = $request->validate([
            'bedrooms'    => 'required|integer|min:0',
            'bathrooms'   => 'required|integer|min:0',
            'area_sqft'   => 'required|numeric|min:0',
            'furnishings' => 'required|string', // Terima string dulu
            // Tambahkan validasi field lain jika Flutter mengirimnya
            // 'property_type' => 'sometimes|string',
        ]);

        // 2. Siapkan data untuk dikirim ke Flask API
        //    Mungkin perlu konversi/mapping dari input Flutter ke format yang dibutuhkan Flask
        //    Contoh: Konversi 'furnishings' string ke integer 0/1 jika Flask butuh itu
        $furnishingInt = 0; // Default Unfurnished
        if (strtolower($validatedData['furnishings']) === 'full furnished') {
            $furnishingInt = 1;
        } else if (strtolower($validatedData['furnishings']) === 'semi furnished') {
            $furnishingInt = 1; // Atau 0? Sesuaikan dengan logic model Anda
        }
        // Contoh: Jika Flask butuh field 'sizeMin' bukan 'area_sqft'
        // $sizeMinValue = $validatedData['area_sqft'];

        // **PENTING**: Tentukan field apa saja yang *sebenarnya* dibutuhkan oleh Flask API Anda
        // Berdasarkan method store() web Anda, sepertinya Flask butuh lebih banyak data?
        // Seperti 'verified', 'view_type', dll. Apakah Flutter mengirim ini?
        // Jika tidak, Anda perlu memberi nilai default atau menghapusnya dari data yang dikirim ke Flask.

        // **Asumsi:** Flask hanya butuh 'bedrooms', 'bathrooms', 'sizeMin' (dari area_sqft), dan 'furnishing' (int)
        $dataForFlask = [
            'bedrooms'   => $validatedData['bedrooms'],
            'bathrooms'  => $validatedData['bathrooms'],
            'sizeMin'    => $validatedData['area_sqft'], // Asumsi mapping
            'furnishing' => $furnishingInt,             // Hasil konversi
            // === Tambahkan field lain di sini jika Flask butuh ===
            // Misal, jika 'verified' selalu 1 untuk prediksi mobile?
            // 'verified' => 1,
            // 'view_type' => 0, // default?
            // 'listing_age_category' => 0, // default?
            // 'title_keyword' => 0, // default?
            // =====================================================
        ];

        // Ambil URL Flask API dari .env
        $flaskApiUrl = env('FLASK_PREDICT_URL', 'http://localhost:5000/prediksi/create'); // Pastikan URL & endpoint Flask benar

        if (!$flaskApiUrl) {
            Log::error('Flask API URL not configured.');
            return response()->json(['success' => false, 'message' => 'Konfigurasi server bermasalah.'], 500);
        }

        try {
            // 3. Kirim Request ke Flask API
            Log::info('Sending data to Flask for mobile prediction:', $dataForFlask); // Log data yang dikirim
            $response = Http::timeout(15)->post($flaskApiUrl, $dataForFlask);

            // 4. Tangani Response dari Flask
            if ($response->successful()) {
                $predictionResult = $response->json();
                Log::info('Received successful response from Flask:', $predictionResult); // Log response sukses

                if (isset($predictionResult['prediction_result'])) {
                    $predictionResultAED = $predictionResult['prediction_result'];
                    // Opsional: Konversi ke IDR jika mobile juga perlu
                    // $aedToIdrRate = 4560;
                    // $predictionResultIDR = floatval($predictionResultAED) * $aedToIdrRate;

                    // Opsional: Simpan riwayat ke MongoDB
                    // try {
                    //     $collectionName = 'riwayat_prediksi';
                    //     $dataToInsert = array_merge(
                    //         $validatedData, // Data asli dari flutter
                    //         ['data_sent_to_flask' => $dataForFlask], // Data yg dikirim ke flask
                    //         ['hasil_prediksi_aed' => $predictionResultAED],
                    //         // ['hasil_prediksi_idr' => $predictionResultIDR],
                    //         ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                    //         ['source' => 'mobile'] // Tandai sumber
                    //         // ['user_id' => $request->user()->id] // Jika endpoint pakai auth
                    //     );
                    //     DB::connection('mongodb')->table($collectionName)->insert($dataToInsert);
                    // } catch (\Exception $dbExc) {
                    //     Log::error('Gagal menyimpan riwayat prediksi (mobile) ke MongoDB: ' . $dbExc->getMessage());
                    //     // Jangan gagalkan response ke user hanya karena gagal simpan log (biasanya)
                    // }

                    // 5. Kirim Response Sukses (JSON) ke Flutter
                    return response()->json([
                        'success' => true,
                        'message' => 'Prediksi harga berhasil didapatkan.',
                        'predicted_price' => $predictionResultAED // Kirim hasil AED
                        // 'predicted_price_idr' => $predictionResultIDR // Jika perlu IDR juga
                    ]);
                } else {
                    Log::error('Invalid response structure from Flask API (mobile)', ['response' => $predictionResult]);
                    return response()->json(['success' => false, 'message' => 'Gagal memproses hasil prediksi.'], 500);
                }
            } else {
                // Jika request ke Flask gagal
                Log::error('Failed to connect to Flask API (mobile)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi layanan prediksi.'
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ConnectionException to Flask API (mobile): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke layanan prediksi.'], 503);
        } catch (\Exception $e) {
            Log::error('General error during mobile prediction: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal.'], 500);
        }
    }
}
