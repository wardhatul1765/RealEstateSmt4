<?php

namespace App\Http\Controllers;

use App\Models\UserProperty; //
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
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

        $query = UserProperty::where('user_id', $user->id); //

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

        // Aturan validasi untuk store
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
                'image' => $uploadedImageNames, // Ini sudah array nama file
                'propertyType' => $validated['propertyType'],
                'mainView' => $validated['mainView'] ?? null,
                'listingAgeCategory' => $validated['listingAgeCategory'] ?? null, //
                'propertyLabel' => $validated['propertyLabel'] ?? null,
            ];

            Log::info('Data to be created in UserProperty:', $propertyDataToCreate);
            $property = UserProperty::create($propertyDataToCreate); //
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
        // Log semua data request (termasuk fields dari Flutter)
        Log::info("Request to update property ID {$id} received (raw form data/fields):", $request->all()); 
        Log::info("Files received for update property ID {$id}:", $request->allFiles());


        $user = auth('api')->user();
        if (!$user) {
            Log::error("Unauthorized attempt to update property {$id}: No authenticated user.");
            return response()->json(['error' => 'Unauthorized: No user found'], 401);
        }

        $property = UserProperty::where('_id', $id)->where('user_id', $user->id)->first(); //

        if (!$property) {
            Log::error("Property with ID {$id} not found or user {$user->id} not authorized to update.");
            return response()->json(['error' => 'Property not found or you are not authorized to update this property.'], 404);
        }
        
        // ================== AWAL PERBAIKAN INPUT UNTUK VALIDASI ==================
        // Untuk PUT multipart, ambil data dari $request->post() atau $request->input()
        // $request->all() mungkin tidak selalu bekerja baik untuk multipart PUT fields
        $inputForValidation = $request->input(); 
        // Jika $request->input() kosong, coba alternatif. Ini mungkin tidak perlu jika Flutter mengirim field dengan benar.
        if (empty($inputForValidation) && $request->isMethod('PUT')) {
             Log::warning("Property Update ID {$id} - request->input() is empty for PUT. Trying request->except(files).");
             $inputForValidation = $request->except(array_keys($request->allFiles()));
        }
        Log::info("Property Update ID {$id} - Input being used for Validation:", $inputForValidation);
        // ================== AKHIR PERBAIKAN INPUT UNTUK VALIDASI ==================

        $validator = Validator::make($inputForValidation, [ // Gunakan $inputForValidation
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
            'listingAgeCategory' => 'nullable|string|max:255', //
            'propertyLabel' => 'nullable|string|max:255',
            // 'images' tidak perlu divalidasi di sini karena sudah dihandle terpisah jika ada file baru
            'retainedImageUrls' => 'nullable|json', 
        ]);

        // Log hasil validasi SEBELUM pengecekan ->fails()
        if ($validator->fails()) {
            Log::error("Property Update ID {$id} - VALIDATION FAILED. Errors:", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422); // Kembalikan error jika validasi gagal
        } else {
            Log::info("Property Update ID {$id} - VALIDATION PASSED.");
        }

        $validated = $validator->validated(); // Ini akan berisi data yang lolos validasi
        Log::info("Property Update ID {$id} - Validated Data (after successful validation):", $validated);


        // Penanganan field image dari DB
        $currentImageValueFromDB = $property->image;
        Log::info("Property Update ID {$id} - Raw 'image' field from DB (type: " . gettype($currentImageValueFromDB) . "): ", [$currentImageValueFromDB]);

        $currentImageNames = [];
        if (is_string($currentImageValueFromDB)) {
            $decodedImages = json_decode($currentImageValueFromDB, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $currentImageNames = $decodedImages;
                Log::info("Property Update ID {$id} - Manually decoded 'image' field from string to array: ", $currentImageNames);
            } else {
                Log::warning("Property Update ID {$id} - 'image' field is a string but FAILED to decode as JSON. Original string: " . $currentImageValueFromDB . ". Error: " . json_last_error_msg());
                $currentImageNames = [];
            }
        } elseif (is_array($currentImageValueFromDB)) {
            $currentImageNames = $currentImageValueFromDB;
            Log::info("Property Update ID {$id} - 'image' field from DB is already an array: ", $currentImageNames);
        } else {
            $currentImageNames = [];
            Log::info("Property Update ID {$id} - 'image' field from DB is null or unknown type, defaulted to empty array.");
        }
        $currentImageNames = $currentImageNames ?? [];
        Log::info("Property Update ID {$id} - Effective Current Image Names from DB (guaranteed array):", $currentImageNames);
        
        // Penanganan retainedImageUrls
        $retainedImageNames = [];
        // Gunakan $request->input('retainedImageUrls') karena ini adalah field teks, bukan file
        $retainedImageUrlsJson = $request->input('retainedImageUrls');
        Log::info("Property Update ID {$id} - Received retainedImageUrls (raw string from request->input()):", [$retainedImageUrlsJson]);

        if (!empty($retainedImageUrlsJson)) {
            $decodedRetainedUrls = json_decode($retainedImageUrlsJson, true);
            Log::info("Property Update ID {$id} - Decoded retainedImageUrls (array):", is_array($decodedRetainedUrls) ? $decodedRetainedUrls : ['Decode failed or not an array. Error: ' . json_last_error_msg()]);

            if (is_array($decodedRetainedUrls)) {
                foreach ($decodedRetainedUrls as $url) {
                    if (!is_string($url) || empty(trim($url))) {
                        Log::warning("Property Update ID {$id} -- LOOP -- Invalid URL in retainedImageUrls (not a string or empty):", [$url]);
                        continue;
                    }
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $filename = basename($url);
                        Log::info("Property Update ID {$id} -- LOOP -- Processing URL: '{$url}'");
                        Log::info("Property Update ID {$id} -- LOOP -- Extracted Filename: '{$filename}' (length: " . strlen($filename) . ")");
                        Log::info("Property Update ID {$id} -- LOOP -- Current DB Images for comparison: ", $currentImageNames);
                        
                        $matchFound = false;
                        foreach($currentImageNames as $dbFilename) {
                            Log::info("Property Update ID {$id} -- LOOP -- Comparing: '{$filename}' (len: ".strlen(trim($filename)).") vs '{$dbFilename}' (len: ".strlen(trim($dbFilename)).")");
                            if (trim($filename) === trim($dbFilename)) {
                                $retainedImageNames[] = trim($filename); // Gunakan trim filename
                                Log::info("Property Update ID {$id} -- LOOP -- Matched and retained: '{$filename}'");
                                $matchFound = true;
                                break; 
                            }
                        }
                        if (!$matchFound) {
                            Log::info("Property Update ID {$id} -- LOOP -- Filename '{$filename}' NOT FOUND in DB images.");
                        }
                    } else {
                         Log::warning("Property Update ID {$id} -- LOOP -- Invalid URL format skipped: '{$url}'");
                    }
                }
            }
        }
        Log::info("Property Update ID {$id} - Final Retained Image Names (after processing retainedImageUrls):", $retainedImageNames);
        
        // Hapus gambar lama yang tidak dipertahankan
        foreach ($currentImageNames as $existingFilename) {
            // Pastikan $existingFilename adalah string dan tidak kosong sebelum diproses
            if (is_string($existingFilename) && !empty(trim($existingFilename))) {
                if (!in_array(trim($existingFilename), $retainedImageNames)) { // Bandingkan dengan trim juga
                    if (Storage::disk('public')->exists('properties/' . trim($existingFilename))) {
                        Storage::disk('public')->delete('properties/' . trim($existingFilename));
                        Log::info("Deleted old image file: properties/" . trim($existingFilename) . " for property ID {$id} (because it was not in retainedImageNames)");
                    } else {
                         Log::warning("Attempted to delete 'properties/" . trim($existingFilename) . "' but it was not found in public storage.");
                    }
                }
            } else {
                Log::warning("Property Update ID {$id} - Skipped an invalid or empty filename in currentImageNames for deletion check: ", [$existingFilename]);
            }
        }

        // Upload gambar baru jika ada
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
                    Log::error("Failed to store new file with original name: " . $file->getClientOriginalName() . " for update.");
                }
            }
        } else {
            Log::info("Property Update ID {$id} - No new images uploaded with this update request.");
        }
        Log::info("Property Update ID {$id} - New Uploaded Image Names:", $newUploadedImageNames);
        
        $finalImageNames = array_values(array_unique(array_merge($retainedImageNames, $newUploadedImageNames)));
        Log::info("Property Update ID {$id} - Final Image Names to be saved in DB:", $finalImageNames);

        try {
            $updateData = [];
            // Jika $validated tidak kosong (artinya ada field teks yang valid untuk diupdate)
            if (!empty($validated)) {
                foreach ($validated as $key => $value) {
                    // Kecualikan 'images' dan 'retainedImageUrls' karena sudah dihandle
                    if ($key !== 'images' && $key !== 'retainedImageUrls') {
                        $updateData[$key] = $value;
                    }
                }
            }
            
            // Selalu update field 'image', meskipun $validated kosong (misalnya hanya update gambar)
            $updateData['image'] = $finalImageNames;

            if(array_key_exists('status', $updateData)) {
                Log::info("Property Update ID {$id} - 'status' field in \$updateData: " . $updateData['status']);
            } else {
                Log::info("Property Update ID {$id} - 'status' field is NOT in \$updateData. Check if it was sent by Flutter and passed validation. Validated data was: ", $validated);
            }
            
            Log::info("Property Update ID {$id} - Complete Data for DB Update (before fill):", $updateData);
            
            // Hanya lakukan fill dan save jika ada data yang akan diupdate
            if (!empty($updateData)) {
                $property->fill($updateData);
                $property->save();
                Log::info("Property ID {$id} updated successfully in DB. Current property state:", $property->fresh()->toArray());
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
}