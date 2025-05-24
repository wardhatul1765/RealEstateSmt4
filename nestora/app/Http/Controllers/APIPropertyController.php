<?php

namespace App\Http\Controllers;

use App\Models\UserProperty; // Pastikan model ini benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Import Validator

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function store(Request $request)
    {
        Log::info('User Auth:', ['user' => auth()->user()]);
        Log::info('Request to store property received', $request->all());
        Log::info('Files received:', $request->allFiles()); // Log khusus untuk file

        $user = auth('api')->user();
        if (!$user) {
            Log::error('Unauthorized attempt to store property: No authenticated user.');
            return response()->json(['error' => 'Unauthorized: No user found'], 401);
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:1000',
            'description' => 'required|string|max:5000',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'sizeMin' => 'required|numeric|min:0', // Ini adalah areaSqft dari Flutter
            'furnishing' => 'required|string|max:255',
            'propertyType' => 'required|string|max:255', // Tambahkan validasi
            'status' => 'required|string|in:pendingVerification,draft', // Sesuaikan jika ada status lain dari Flutter
            'mainView' => 'nullable|string|max:255', // Tambahkan validasi (nullable jika opsional)
            'listingAgeCategory' => 'nullable|string|max:255', // Tambahkan validasi
            'propertyLabel' => 'nullable|string|max:255', // Tambahkan validasi
            'images' => 'required|array|min:1', // Diubah dari 'image' menjadi 'images' agar konsisten dengan log Flutter
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Max 2MB per image
            'retainedImageUrls' => 'nullable|json', // Jika Anda mengirim ini sebagai JSON string
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for store property', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated(); // Ambil data yang sudah divalidasi

        // Proses upload file dari field 'images'
        $uploadedFilenames = [];
        if ($request->hasFile('images')) { // Periksa apakah ada file di field 'images'
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/properties', $filename);
                // Simpan path relatif untuk fleksibilitas, atau URL lengkap jika preferensi
                // $uploadedFilenames[] = 'properties/' . $filename; 
                $uploadedFilenames[] = url('storage/properties/' . $filename); // URL lengkap
            }
        } else {
            // Jika validasi 'images' => 'required' aktif, ini seharusnya tidak terjadi
            // Namun, ini bisa jadi fallback jika logika validasi diubah
             Log::warning('No files found in "images" field despite passing validation.', $request->allFiles());
        }


        // Buat properti dengan data yang sudah divalidasi
        try {
            $property = UserProperty::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'bedrooms' => (int) $validated['bedrooms'],
                'bathrooms' => (int) $validated['bathrooms'],
                'sizeMin' => (float) $validated['sizeMin'], // Ini menyimpan areaSqft
                'furnishing' => $validated['furnishing'],
                'address' => $validated['address'],
                'status' => $validated['status'], // Ambil dari data yang divalidasi
                'user_id' => $user->_id, // Atau $user->id tergantung primary key User Anda
                'image' => $uploadedFilenames, // Array URL gambar

                // Field yang hilang sebelumnya, sekarang ditambahkan:
                'propertyType' => $validated['propertyType'],
                'mainView' => $validated['mainView'] ?? null, // Berikan null jika tidak ada
                'listingAgeCategory' => $validated['listingAgeCategory'] ?? null,
                'propertyLabel' => $validated['propertyLabel'] ?? null,

                // Pastikan tidak ada field 'Sold' di sini kecuali memang disengaja
            ]);

            Log::info('Property created successfully', ['property_id' => $property->id]);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully with images',
                'data' => $property,
            ], 201); // Kode status 201 untuk resource created

        } catch (\Exception $e) {
            Log::error('Error creating property in database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to create property in database.'], 500);
        }
    }

    // TODO: Tambahkan metode update, show, delete jika diperlukan
    // Metode update akan mirip, tapi Anda akan mencari properti yang ada dulu
    // dan menangani 'retainedImageUrls' serta gambar baru.
}