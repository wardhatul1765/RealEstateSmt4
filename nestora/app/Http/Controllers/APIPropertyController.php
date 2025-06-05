<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PropertyView;
use App\Models\UserProperty;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Auth;

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->except(['getPublicProperties', 'showPublicProperty', 'recordView']);
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
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10000',
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
        }
        Log::info("Property Update ID {$id} - VALIDATION PASSED.");
        

        $validated = $validator->validated();
        Log::info("Property Update ID {$id} - Validated Data (after successful validation):", $validated);

        $currentImageValueFromDB = $property->image;
        $currentImageNames = [];
        if (is_string($currentImageValueFromDB)) {
            $decodedImages = json_decode($currentImageValueFromDB, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $currentImageNames = $decodedImages;
            }
        } elseif (is_array($currentImageValueFromDB)) {
            $currentImageNames = $currentImageValueFromDB;
        }
        
        $retainedImageNames = [];
        $retainedImageUrlsJson = $request->input('retainedImageUrls');
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
                    $newUploadedImageNames[] = $filename;
                }
            }
        }
        
        $finalImageNames = array_values(array_unique(array_merge($retainedImageNames, $newUploadedImageNames)));
        
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
            
            if (!empty($updateData)) {
                $property->fill($updateData);
                $property->save();
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
            $query = UserProperty::where('status', $statusToQuery);

            // Keyword Search
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                Log::info("APIPropertyController: Mencari dengan keyword: '$keyword'");
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('address', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%")
                      ->orWhere('propertyType', 'LIKE', "%{$keyword}%");
                });
            }

            // Default Sorting
            $defaultSortField = 'updated_at';
            $defaultSortDirection = 'desc';

            // Category Filter
            if ($request->filled('category')) {
                $category = $request->category;
                Log::info("APIPropertyController: Menerima parameter kategori: '$category'");
                if ($category === "Most Viewed") {
                    $defaultSortField = 'total_views_count';
                    $defaultSortDirection = 'desc';
                }
            }

            // --- Advanced Filters ---
            if ($request->filled('propertyType')) {
                $query->where('propertyType', $request->propertyType);
            }

            // Lokasi (LIKE address)
            if ($request->filled('lokasi')) {
                $query->where('address', 'LIKE', '%' . $request->lokasi . '%');
            }

            if ($request->filled('minPrice') && is_numeric($request->minPrice)) {
                $query->where('price', '>=', (float)$request->minPrice);
            }
            if ($request->filled('maxPrice') && is_numeric($request->maxPrice)) {
                $query->where('price', '<=', (float)$request->maxPrice);
            }

            if ($request->filled('minBedrooms') && is_numeric($request->minBedrooms)) {
                $query->where('bedrooms', '>=', (int)$request->minBedrooms);
            }
            if ($request->filled('maxBedrooms') && is_numeric($request->maxBedrooms)) {
                $query->where('bedrooms', '<=', (int)$request->maxBedrooms);
            }

            if ($request->filled('minBathrooms') && is_numeric($request->minBathrooms)) {
                $query->where('bathrooms', '>=', (int)$request->minBathrooms);
            }
            if ($request->filled('maxBathrooms') && is_numeric($request->maxBathrooms)) {
                $query->where('bathrooms', '<=', (int)$request->maxBathrooms);
            }
            
            if ($request->filled('furnishing')) {
                $query->where('furnishing', $request->furnishing);
            }

            if ($request->filled('minArea') && is_numeric($request->minArea)) {
                $query->where('sizeMin', '>=', (float)$request->minArea);
            }

            // Filter baru
            if ($request->filled('mainView')) {
                $query->where('mainView', $request->mainView);
            }
            if ($request->filled('listingAgeCategory')) {
                $query->where('listingAgeCategory', $request->listingAgeCategory);
            }
            if ($request->filled('propertyLabel')) {
                $query->where('propertyLabel', $request->propertyLabel);
            }
            // --- End Advanced Filters ---

            $properties = $query->orderBy($defaultSortField, $defaultSortDirection)
                                ->paginate($request->input('limit', 10));

            Log::info("APIPropertyController: Query executed for getPublicProperties. Found " . $properties->total() . " items matching criteria with filters: ", $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Properti publik berhasil diambil.',
                'data' => $properties 
            ], 200);

        } catch (\Exception $e) {
            Log::error('APIPropertyController Error fetching public properties: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request_params' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data properti publik.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showPublicProperty(Request $request, $id)
    {
        Log::info("APIPropertyController: showPublicProperty dipanggil untuk ID: " . $id);
        $property = UserProperty::with('owner')->find($id);

        if (!$property || $property->status !== 'approved') {
            Log::warning("APIPropertyController: Properti {$id} tidak ditemukan atau status bukan approved.");
            return response()->json(['success' => false, 'message' => 'Properti tidak ditemukan atau tidak tersedia.'], 404);
        }

        try {
            PropertyView::create([
                'property_id' => $property->_id,
                'user_id' => auth('api')->check() ? auth('api')->id() : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'viewed_at' => now(),
            ]);
            
            $currentViews = $property->total_views_count ?? 0;
            $property->total_views_count = $currentViews + 1;
            $property->save();
            $property->refresh();

        } catch (\Exception $e) {
            Log::error("APIPropertyController: GAGAL mencatat view atau increment untuk properti {$id}: " . $e->getMessage(), ['exception' => $e]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail properti berhasil diambil.',
            'data' => $property,
        ]);
    }

    public function getPropertyViewStatistics(Request $request, $id)
    {
        Log::info("APIPropertyController: getPropertyViewStatistics dipanggil untuk ID properti (dari route): " . $id);
        $property = UserProperty::find($id);
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found'], 404);
        }

        $property_id_to_match = $property->_id; 
        $user = auth('api')->user();
        if (!$user || $property->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to view statistics for this property'], 403);
        }

        $dailyStats = [];
        $endDateDaily = new UTCDateTime(Carbon::now('UTC')->endOfDay()->getTimestamp() * 1000);
        $startDateDaily = new UTCDateTime(Carbon::now('UTC')->subDays(29)->startOfDay()->getTimestamp() * 1000);

        $dailyRawCursor = PropertyView::raw(function($collection) use ($property_id_to_match, $startDateDaily, $endDateDaily) {
            return $collection->aggregate([
                ['$match' => ['property_id' => $property_id_to_match, 'viewed_at' => ['$gte' => $startDateDaily, '$lte' => $endDateDaily]]],
                ['$group' => ['_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$viewed_at', 'timezone' => 'Asia/Jakarta']], 'count' => ['$sum' => 1]]],
                ['$sort' => ['_id' => 1]]
            ]);
        });
        $dailyRawArray = iterator_to_array($dailyRawCursor);
        
        $currentDateLoop = Carbon::now('UTC')->subDays(29)->startOfDay();
        $endDateLoop = Carbon::now('UTC')->endOfDay();
        while ($currentDateLoop->lte($endDateLoop)) {
            $dailyStats[$currentDateLoop->format('Y-m-d')] = 0;
            $currentDateLoop->addDay();
        }
        
        foreach ($dailyRawArray as $item) {
            $dateKey = $item['_id'] ?? ($item['App\\Models\\PropertyView']['id'] ?? null);
            $countVal = $item['count'] ?? ($item['App\\Models\\PropertyView']['count'] ?? null);
            if ($dateKey !== null && $countVal !== null && array_key_exists($dateKey, $dailyStats)) {
                $dailyStats[$dateKey] = $countVal;
            }
        }

        $monthlyStats = [];
        $endDateMonthly = new UTCDateTime(Carbon::now('UTC')->endOfMonth()->getTimestamp() * 1000);
        $startDateMonthly = new UTCDateTime(Carbon::now('UTC')->subMonths(11)->startOfMonth()->getTimestamp() * 1000);
        
        $monthlyRawCursor = PropertyView::raw(function($collection) use ($property_id_to_match, $startDateMonthly, $endDateMonthly) {
            return $collection->aggregate([
                ['$match' => ['property_id' => $property_id_to_match, 'viewed_at' => ['$gte' => $startDateMonthly, '$lte' => $endDateMonthly]]],
                ['$group' => ['_id' => ['$dateToString' => ['format' => '%Y-%m', 'date' => '$viewed_at', 'timezone' => 'Asia/Jakarta']], 'count' => ['$sum' => 1]]],
                ['$sort' => ['_id' => 1]]
            ]);
        });
        $monthlyRawArray = iterator_to_array($monthlyRawCursor);
        
        $currentMonthLoop = Carbon::now('UTC')->subMonths(11)->startOfMonth();
        $endMonthLoop = Carbon::now('UTC')->endOfMonth();
        while ($currentMonthLoop->lte($endMonthLoop)) {
            $monthlyStats[$currentMonthLoop->format('Y-m')] = 0;
            $currentMonthLoop->addMonth();
        }

        foreach ($monthlyRawArray as $item) {
            $dateKey = $item['_id'] ?? ($item['App\\Models\\PropertyView']['id'] ?? null);
            $countVal = $item['count'] ?? ($item['App\\Models\\PropertyView']['count'] ?? null);

            if ($dateKey !== null && $countVal !== null && array_key_exists($dateKey, $monthlyStats)) {
                 $monthlyStats[$dateKey] = $countVal;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Property view statistics fetched successfully.',
            'data' => [
                'daily' => $dailyStats,
                'monthly' => $monthlyStats,
            ]
        ]);
    }    

    public function destroy(Request $request, $id)
    {
        Log::info("Request to delete property ID {$id} received.");
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: No user found'], 401);
        }

        $property = UserProperty::where('_id', $id)->where('user_id', $user->id)->first();
        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Property not found or you are not authorized to delete this property.'], 404);
        }

        try {
            $imageFilenamesData = $property->image;
            $imageFilenamesToDelete = [];

            if (is_string($imageFilenamesData)) {
                $decoded = json_decode($imageFilenamesData, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $imageFilenamesToDelete = $decoded;
                } elseif (!empty($imageFilenamesData)) {
                    $imageFilenamesToDelete = [$imageFilenamesData];
                }
            } elseif (is_array($imageFilenamesData)) {
                $imageFilenamesToDelete = $imageFilenamesData;
            }

            if (!empty($imageFilenamesToDelete)) {
                foreach ($imageFilenamesToDelete as $filename) {
                    if (is_string($filename) && !empty(trim($filename))) {
                        $filePath = 'public/properties/' . trim($filename);
                        if (Storage::exists($filePath)) {
                            Storage::delete($filePath);
                        }
                    }
                }
            }
            $property->delete();
            return response()->json(['success' => true, 'message' => 'Property deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting property ID {$id}", ['error_message' => $e->getMessage(), 'error_trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => "Failed to delete property ID {$id}. Please check server logs."], 500);
        }
    }

    // === METHOD BARU UNTUK BOOKMARK ===
    public function toggleBookmark(Request $request, $id) // $id adalah _id properti
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            Log::error('ToggleBookmark: Unauthorized access attempt. User not authenticated.');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $property = UserProperty::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("ToggleBookmark: Property with ID '{$id}' not found in database.");
            return response()->json(['success' => false, 'message' => 'Property not found'], 404);
        }

        $userIdString = (string) $user->id;

        $currentBookmarksArray = $property->bookmarkedBy ?? [];
        // Tambahkan pengecekan eksplisit jika $property->bookmarkedBy tidak langsung array
        if (!is_array($currentBookmarksArray)) {
            Log::warning("ToggleBookmark: bookmarkedBy for property '{$id}' was not an array. Attempting json_decode if it's a string.");
            $decoded = json_decode((string)$currentBookmarksArray, true);
            $currentBookmarksArray = is_array($decoded) ? $decoded : [];
        }

        Log::info("ToggleBookmark: User '{$userIdString}' for property '{$id}'. Current bookmarkedBy (after potential decode): " . json_encode($currentBookmarksArray));

        $wasFavorited = in_array($userIdString, $currentBookmarksArray);
        $isFavoritedNow = false;
        $newBookmarks = $currentBookmarksArray;

        if ($wasFavorited) {
            Log::info("ToggleBookmark: User '{$userIdString}' IS IN current bookmarkedBy. Action: Removing.");
            $newBookmarks = array_values(array_filter($currentBookmarksArray, function ($uid) use ($userIdString) {
                return (string) $uid !== $userIdString;
            }));
            $message = 'Properti dihapus dari bookmark.';
            $isFavoritedNow = false;
        } else {
            Log::info("ToggleBookmark: User '{$userIdString}' IS NOT IN current bookmarkedBy. Action: Adding.");
            if (!in_array($userIdString, $newBookmarks)) {
                 $newBookmarks[] = $userIdString;
            }
            $message = 'Properti ditambahkan ke bookmark.';
            $isFavoritedNow = true;
        }

        $property->bookmarkedBy = $newBookmarks;
        Log::info("ToggleBookmark: Model's bookmarkedBy attribute set to (before save): " . json_encode($property->bookmarkedBy));

        try {
            $property->save();
            Log::info("ToggleBookmark: Property '{$id}' successfully SAVED to database.");
        } catch (\Exception $e) {
            Log::error("ToggleBookmark: FAILED to save property '{$id}' to database.", [
                'error_message' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan status bookmark ke database.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => ['is_favorited' => $isFavoritedNow]
        ]);
    }

    public function getBookmarkedProperties(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            Log::warning('GetBookmarkedProperties: Unauthorized access attempt.');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userIdToQuery = (string) $user->id; // Pastikan ID user adalah string
        Log::info("GetBookmarkedProperties: Fetching bookmarks for User ID (string): '{$userIdToQuery}'");

        // Query ini akan mencari properti di mana $userIdToQuery adalah salah satu elemen dalam array bookmarkedBy
        $propertiesPaginated = UserProperty::where('bookmarkedBy', $userIdToQuery)
                                      ->with('owner') // Eager load data pemilik
                                      ->latest() // Urutkan berdasarkan terbaru
                                      ->paginate($request->input('limit', 20)); // Paginasi

        Log::info("GetBookmarkedProperties: Found " . $propertiesPaginated->total() . " bookmarked properties for user '{$userIdToQuery}'.");

        return response()->json(['success' => true, 'data' => $propertiesPaginated]);
    }
    // === AKHIR METHOD BOOKMARK ===
}