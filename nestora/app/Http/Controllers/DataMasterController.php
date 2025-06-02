<?php

namespace App\Http\Controllers;

use App\Models\UserProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataMasterController extends Controller
{
    // ... (fungsi lain tidak perlu diubah) ...
    public function propertiIndex(Request $request)
    {
        $query = UserProperty::query()->latest('created_at');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        $dataProperty = $query->paginate(15);
        return view('data_master.index', compact('dataProperty'));
    }

    public function getPropertyEditData($id)
    {
        try {
            $property = UserProperty::findOrFail($id);
            return response()->json($property);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Properti tidak ditemukan.'], 404);
        }
    }

    protected function validateProperty(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'propertyType' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'sizeMin' => 'nullable|numeric|min:0',
            'furnishing' => 'nullable|string|in:Yes,No,Partly',
            'status' => 'required|string',
            'mainView' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'propertyLabel' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'listingAgeCategory' => 'nullable|string',
        ];
        return Validator::make($request->all(), $rules);
    }
    
    public function store(Request $request)
    {
        $validator = $this->validateProperty($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validatedData = $validator->validated();

        try {
            if ($request->hasFile('images')) {
                // [FIX] Nama variabel disamakan menjadi $imageNames
                $imageNames = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('public/properties');
                    // [FIX] Mengisi array $imageNames
                    $imageNames[] = basename($path);
                }
                // [FIX] Menggunakan variabel $imageNames yang sudah terisi
                $validatedData['image'] = $imageNames;
            } else {
                $validatedData['image'] = [];
            }

            $property = UserProperty::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil ditambahkan.',
                'property' => $property
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saat simpan properti: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan data properti.', 'errors' => ['general' => [$e->getMessage()]]], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateProperty($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validatedData = $validator->validated();
        
        try {
            $property = UserProperty::findOrFail($id);

            if ($request->hasFile('images')) {
                // Hapus gambar lama (logika ini sudah benar)
                if ($property->getRawOriginal('image')) {
                    $oldImages = json_decode($property->getRawOriginal('image'), true) ?: [];
                    foreach ($oldImages as $oldFilename) {
                        $storagePath = 'public/properties/' . $oldFilename;
                        if (Storage::exists($storagePath)) {
                            Storage::delete($storagePath);
                        }
                    }
                }

                // [FIX] Nama variabel disamakan menjadi $newImageNames
                $newImageNames = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('public/properties');
                    // [FIX] Mengisi array $newImageNames
                    $newImageNames[] = basename($path);
                }
                // [FIX] Menggunakan variabel $newImageNames yang sudah terisi
                $validatedData['image'] = $newImageNames;
            } else {
                unset($validatedData['image']);
            }

            $property->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil diperbarui.',
                'property' => $property->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat update properti: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui data properti.', 'errors' => ['general' => [$e->getMessage()]]], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        // ... (Fungsi destroy sudah benar, tidak perlu diubah)
        try {
            $property = UserProperty::findOrFail($id);
            if ($property->getRawOriginal('image')) {
                $imagesToDelete = json_decode($property->getRawOriginal('image'), true) ?: [];
                foreach ($imagesToDelete as $filename) {
                    $storagePath = 'public/properties/' . $filename;
                    if (Storage::exists($storagePath)) {
                        Storage::delete($storagePath);
                    }
                }
            }
            $property->delete();
            return response()->json(['success' => true, 'message' => 'Data properti berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error saat hapus properti: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus data properti.', 'deleteError' => $e->getMessage()], 500);
        }
    }
}