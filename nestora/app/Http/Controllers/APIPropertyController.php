<?php

namespace App\Http\Controllers;

use App\Models\UserProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->except(['getPublicProperties']);
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized: No user found'], 401);
        }

        Log::info('Request to fetch user properties received', [
            'user_id' => $user->id,
            'query_params' => $request->query()
        ]);

        $query = UserProperty::where('user_id', $user->id);

        if ($request->has('status')) {
            $statuses = explode(',', $request->query('status'));
            $validStatuses = array_filter(array_map('trim', $statuses), function($statusString) {
                return in_array($statusString, ['draft', 'pendingVerification', 'approved', 'rejected', 'sold', 'archived']);
            });

            if (!empty($validStatuses)) {
                $query->whereIn('status', $validStatuses);
            }
        }

        $properties = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'User properties fetched successfully.',
            'data' => $properties,
        ]);
    }
    
    public function store(Request $request)
    {
        Log::info('User Auth:', ['user' => auth()->user()]);
        Log::info('Request to store property received (raw):', $request->all());
        Log::info('Files received:', $request->allFiles());

        $user = auth('api')->user();
        if (!$user) {
            Log::error('Unauthorized attempt to store property: No authenticated user.');
            return response()->json(['error' => 'Unauthorized: No user found'], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:1000', 
            'description' => 'required|string|max:5000',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'sizeMin' => 'required|numeric|min:0',
            'furnishing' => 'required|string|max:255',
            'propertyType' => 'required|string|max:255',
            'status' => 'required|string|in:pendingVerification,draft,approved,rejected,sold,archived', 
            'mainView' => 'nullable|string|max:255',
            'listingAgeCategory' => 'nullable|string|max:255', 
            'propertyLabel' => 'nullable|string|max:255',
            'images' => 'required|array|min:1', 
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for store property', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        Log::info('Store Property - Validated Data:', $validated);

        $uploadedImageNames = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) { 
                    Log::error("Invalid file uploaded: " . $file->getClientOriginalName());
                    continue; 
                }
                $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/properties', $filename);
                
                if ($path) {
                    Log::info("File {$filename} successfully stored at {$path}");
                    $uploadedImageNames[] = $filename;
                } else {
                    Log::error("Failed to store file with original name: " . $file->getClientOriginalName());
                }
            }
        } else {
            Log::warning('No files found in "images" field despite passing validation (store).', $request->allFiles());
        }
        
        if (empty($uploadedImageNames) && isset($validated['images'])) {
            Log::error('Image upload was required/attempted but no files were successfully processed and stored.');
            return response()->json(['errors' => ['images' => ['At least one image is required and failed to upload.']]], 422);
        }

        try {
            $propertyDataToCreate = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => (float) $validated['price'],
                'bedrooms' => (int) $validated['bedrooms'],
                'bathrooms' => (int) $validated['bathrooms'],
                'sizeMin' => (float) $validated['sizeMin'],
                'furnishing' => $validated['furnishing'],
                'address' => $validated['address'],
                'status' => $validated['status'],
                'user_id' => $user->id,
                'image' => $uploadedImageNames,
                'propertyType' => $validated['propertyType'],
                'mainView' => $validated['mainView'] ?? null,
                'listingAgeCategory' => $validated['listingAgeCategory'] ?? null,
                'propertyLabel' => $validated['propertyLabel'] ?? null,
            ];

            Log::info('Data to be created in UserProperty:', $propertyDataToCreate);
            $property = UserProperty::create($propertyDataToCreate);
            Log::info('Property created successfully', ['property_id' => $property->id]);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully with images',
                'data' => $property,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating property in database', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to create property in database. Check logs.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('User Auth for update:', ['user' => auth()->user()]);
        Log::info("Request to update property ID {$id} received (raw form data/fields):", $request->all()); 
        Log::info("Files received for update property ID {$id}:", $request->allFiles());

        $user = auth('api')->user();
        if (!$user) {
            Log::error("Unauthorized attempt to update property {$id}: No authenticated user.");
            return response()->json(['error' => 'Unauthorized: No user found'], 401);
        }

        $property = UserProperty::where('_id', $id)->where('user_id', $user->id)->first();

        if (!$property) {
            Log::error("Property with ID {$id} not found or user {$user->id} not authorized to update.");
            return response()->json(['error' => 'Property not found or you are not authorized to update this property.'], 404);
        }
        
        $inputForValidation = $request->input(); 
        if (empty($inputForValidation) && $request->isMethod('PUT')) { // Anda menggunakan POST untuk update dari Flutter, jadi ini mungkin tidak ter-trigger
             Log::warning("Property Update ID {$id} - request->input() is empty for PUT/POST. Trying request->except(files).");
             $inputForValidation = $request->except(array_keys($request->allFiles()));
        }
        Log::info("Property Update ID {$id} - Input being used for Validation:", $inputForValidation);

        $validator = Validator::make($inputForValidation, [
            'title' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'address' => 'sometimes|required|string|max:1000',
            'description' => 'sometimes|required|string|max:5000',
            'bedrooms' => 'sometimes|required|integer|min:0',
            'bathrooms' => 'sometimes|required|integer|min:0',
            'sizeMin' => 'sometimes|required|numeric|min:0',
            'furnishing' => 'sometimes|required|string|max:255',
            'propertyType' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:pendingVerification,draft,approved,rejected,sold,archived',
            'mainView' => 'nullable|string|max:255',
            'listingAgeCategory' => 'nullable|string|max:255',
            'propertyLabel' => 'nullable|string|max:255',
            'retainedImageUrls' => 'nullable|json', 
        ]);

        if ($validator->fails()) {
            Log::error("Property Update ID {$id} - VALIDATION FAILED. Errors:", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            Log::info("Property Update ID {$id} - VALIDATION PASSED.");
        }

        $validated = $validator->validated();
        Log::info("Property Update ID {$id} - Validated Data (after successful validation):", $validated);

        $currentImageValueFromDB = $property->image;
        Log::info("Property Update ID {$id} - Raw 'image' field from DB (type: " . gettype($currentImageValueFromDB) . "): ", [$currentImageValueFromDB]);

        $currentImageNames = [];
        if (is_string($currentImageValueFromDB)) {
            $decodedImages = json_decode($currentImageValueFromDB, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $currentImageNames = $decodedImages;
            }
        } elseif (is_array($currentImageValueFromDB)) {
            $currentImageNames = $currentImageValueFromDB;
        }
        Log::info("Property Update ID {$id} - Effective Current Image Names from DB (guaranteed array):", $currentImageNames);
        
        $retainedImageNames = [];
        $retainedImageUrlsJson = $request->input('retainedImageUrls');
        Log::info("Property Update ID {$id} - Received retainedImageUrls (raw string from request->input()):", [$retainedImageUrlsJson]);

        if (!empty($retainedImageUrlsJson)) {
            $decodedRetainedUrls = json_decode($retainedImageUrlsJson, true);
            if (is_array($decodedRetainedUrls)) {
                foreach ($decodedRetainedUrls as $url) {
                    if (is_string($url) && !empty(trim($url)) && filter_var($url, FILTER_VALIDATE_URL)) {
                        $filename = basename($url);
                        if (in_array(trim($filename), array_map('trim', $currentImageNames))) {
                            $retainedImageNames[] = trim($filename);
                        }
                    }
                }
            }
        }
        Log::info("Property Update ID {$id} - Final Retained Image Names (after processing retainedImageUrls):", $retainedImageNames);
        
        foreach ($currentImageNames as $existingFilename) {
            if (is_string($existingFilename) && !empty(trim($existingFilename))) {
                if (!in_array(trim($existingFilename), $retainedImageNames)) {
                    if (Storage::disk('public')->exists('properties/' . trim($existingFilename))) {
                        Storage::disk('public')->delete('properties/' . trim($existingFilename));
                        Log::info("Deleted old image file: properties/" . trim($existingFilename) . " for property ID {$id}");
                    }
                }
            }
        }

        $newUploadedImageNames = [];
        if ($request->hasFile('images')) {
            Log::info("Property Update ID {$id} - Processing new image uploads.");
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) {
                    Log::error("Invalid file uploaded during update: " . $file->getClientOriginalName());
                    continue; 
                }
                $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/properties', $filename);
                if ($path) {
                    Log::info("New file {$filename} successfully stored at {$path} for update.");
                    $newUploadedImageNames[] = $filename;
                } else {
                    Log::error("Failed to store new file: " . $file->getClientOriginalName() . " for update.");
                }
            }
        }
        Log::info("Property Update ID {$id} - New Uploaded Image Names:", $newUploadedImageNames);
        
        $finalImageNames = array_values(array_unique(array_merge($retainedImageNames, $newUploadedImageNames)));
        Log::info("Property Update ID {$id} - Final Image Names to be saved in DB:", $finalImageNames);

        try {
            $updateData = [];
            if (!empty($validated)) {
                foreach ($validated as $key => $value) {
                    if ($key !== 'images' && $key !== 'retainedImageUrls') {
                        $updateData[$key] = $value;
                    }
                }
            }
            $updateData['image'] = $finalImageNames;
            
            Log::info("Property Update ID {$id} - Complete Data for DB Update (before fill):", $updateData);
            
            if (!empty($updateData)) {
                $property->fill($updateData);
                $property->save();
                Log::info("Property ID {$id} updated successfully in DB.");
            } else {
                Log::info("Property Update ID {$id} - No data to update after processing. Property not saved.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => $property->fresh(),
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating property ID {$id} in database", [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => "Failed to update property ID {$id} in database. Check logs."], 500);
        }
    }

    public function getPublicProperties(Request $request)
    {
        try {
            $statusToQuery = 'approved';
            
            // Mulai query dengan model UserProperty dan filter status awal
            $query = UserProperty::where('status', $statusToQuery);

            // +++ AWAL LOGIKA KEYWORD SEARCH +++
            if ($request->has('keyword') && !empty($request->keyword)) {
                $keyword = $request->keyword;
                Log::info("APIPropertyController: Mencari properti publik dengan status: '$statusToQuery' DAN keyword: '$keyword'");

                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('address', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%")
                      ->orWhere('propertyType', 'LIKE', "%{$keyword}%");
                });
            } else {
                Log::info("APIPropertyController: Mencari properti publik dengan status: '$statusToQuery' (tanpa keyword).");
            }
            // +++ AKHIR LOGIKA KEYWORD SEARCH +++

            $properties = $query->orderBy('updated_at', 'desc')
                                ->paginate(10);

            if ($properties->isEmpty()) {
                Log::info("APIPropertyController: Tidak ada properti publik ditemukan dengan kriteria yang diberikan.");
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada properti yang tersedia saat ini sesuai pencarian Anda.',
                    'data' => $properties // Mengembalikan objek paginasi kosong, bukan array kosong langsung
                                         // Ini agar Flutter bisa tetap memproses struktur paginasi
                ], 200);
            }

            Log::info("APIPropertyController: Properti publik ditemukan (" . $properties->total() . " total items, " . $properties->count() . " items di halaman ini). Mengirim respons.");
            return response()->json([
                'success' => true,
                'message' => 'Properti publik berhasil diambil.',
                'data' => $properties 
            ], 200);

        } catch (\Exception $e) {
            Log::error('APIPropertyController Error fetching public properties: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data properti publik.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}