<?php

namespace App\Http\Controllers;

use App\Models\UserProperty; // Pastikan ini model yang benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
// use Carbon\Carbon; // Jika perlu manipulasi tanggal

class DataMasterController extends Controller
{
    /**
     * Menampilkan halaman daftar master properti.
     */
    public function propertiIndex(Request $request)
    {
        $query = UserProperty::query()->latest('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('Address', 'like', '%' . $search . '%');
            });
        }

        $dataProperty = $query->paginate(15); // Sesuaikan jumlah paginasi jika perlu
        return view('data_master.index', compact('dataProperty'));
    }

    /**
     * Mengambil data properti untuk form edit (dipanggil via AJAX).
     * Rute: GET /data-master/properti/{id}/edit-data
     */
    public function getPropertyEditData($id)
    {
        try {
            $property = UserProperty::findOrFail($id);
            // Karena 'image' dan 'status' di-cast di model, Laravel otomatis menanganinya
            return response()->json($property);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Properti tidak ditemukan.'], 404);
        }
    }

    /**
     * Validasi umum untuk store dan update.
     */
    protected function validateProperty(Request $request, $isUpdate = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'Address' => 'nullable|string|max:1000',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'sizeMin' => 'nullable|numeric|min:0',
            'propertyType' => 'nullable|string|max:100', // Sesuai data Alpine
            'furnishing' => 'nullable|string|in:Yes,No,Partly',
            'status' => 'required|in:0,1', // Input dari form akan '0' atau '1'
            'addedOn' => 'nullable|date_format:Y-m-d',
            'mainView' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000', // Tambah max length jika perlu
            'propertyLabel' => 'nullable|string|max:255',
            // 'user_id' => 'nullable|exists:users,id', // Jika ada relasi ke user
            'images' => 'nullable|array', // 'images' adalah nama input dari form untuk file
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048' // Validasi setiap file
        ];

        // Jika update, beberapa field mungkin tidak wajib atau punya aturan berbeda
        // if ($isUpdate) {
        //     // contoh: $rules['field_khusus_update'] = '...';
        // }

        return Validator::make($request->all(), $rules);
    }


    /**
     * Menyimpan properti baru ke database (dipanggil via AJAX).
     * Rute: POST /data-master/properti
     */
    public function store(Request $request)
    {
        $validator = $this->validateProperty($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Proses upload gambar jika ada
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                // Simpan file ke 'storage/app/public/properties'
                $path = $file->store('public/properties');
                $imagePaths[] = Storage::url($path); // Simpan URL publiknya
            }
            // 'image' akan disimpan sebagai JSON karena ada $casts 'array' di Model
            $validatedData['image'] = $imagePaths;
        } else {
            $validatedData['image'] = []; // Default array kosong jika tidak ada gambar
        }

        // 'status' dari request adalah '0' atau '1', akan di-cast jadi boolean oleh Model
        // $validatedData['status'] = $request->input('status') === '1'; // Sudah ditangani $casts

        try {
            $property = UserProperty::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil ditambahkan.',
                'property' => $property // Mengembalikan data properti yang baru dibuat
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error saat simpan properti: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan data properti.', 'errors' => ['general' => [$e->getMessage()]]], 500);
        }
    }

    /**
     * Memperbarui data properti (dipanggil via AJAX).
     * Rute: PUT /data-master/properti/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $property = UserProperty::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Properti tidak ditemukan.'], 404);
        }

        $validator = $this->validateProperty($request, true);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Proses upload gambar jika ada gambar BARU yang diunggah
        if ($request->hasFile('images')) {
            // 1. Hapus gambar lama dari storage (jika ada)
            // Model UserProperty punya $casts 'image' => 'array', jadi $property->image sudah berupa array URL
            if ($property->image && is_array($property->image)) {
                foreach ($property->image as $oldImageUrl) {
                    // Konversi URL ke path storage relatif (misal: public/properties/namafile.jpg)
                    $publicPath = parse_url($oldImageUrl, PHP_URL_PATH); // Dapatkan path dari URL (misal: /storage/properties/file.jpg)
                    $storagePath = 'public' . str_replace(Storage::url(''), '', $publicPath); // Hapus base URL storage, sisakan path relatif ke 'public'
                                                                                              // Atau cara lain jika Storage::url('') tidak sesuai:
                                                                                              // $storagePath = 'public' . substr($publicPath, strlen('/storage'));
                    if (Storage::exists($storagePath)) {
                        Storage::delete($storagePath);
                    } else {
                        Log::warning("File gambar lama tidak ditemukan untuk dihapus: " . $storagePath . " dari URL: " . $oldImageUrl);
                    }
                }
            }

            // 2. Simpan gambar baru
            $newImagePaths = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('public/properties');
                $newImagePaths[] = Storage::url($path);
            }
            $validatedData['image'] = $newImagePaths; // Timpa dengan array URL gambar baru
        } else {
            // Jika tidak ada file gambar baru yang diunggah, jangan ubah field 'image'
            // Hapus 'images' dari $validatedData agar tidak menimpa data 'image' lama dengan null/empty.
            // $validatedData['image'] akan tetap berisi path dari $validator->validated() jika 'images' tidak ada di request.
            // Kita hanya ingin update 'image' jika ada file baru.
            // Jadi, jika tidak ada file baru, $validatedData['image'] tidak perlu di-set,
            // karena $property->image sudah berisi data lama.
            // Namun, karena $validator membuat 'images' bisa jadi array kosong jika dikirim,
            // lebih aman untuk tidak menyentuh 'image' jika tidak ada file baru.
            unset($validatedData['images']); // Hapus key 'images' agar kolom 'image' di DB tidak terupdate jika tidak ada file baru
                                            // Kolom 'image' akan tetap pakai nilai lama dari $property.
                                            // Ini hanya jika $validatedData['image'] tidak ada di $fillable atau $guarded
                                            // dan kita update $property->fill($validatedData); $property->save();
                                            // Jika pakai $property->update($validatedData), field 'image' yg tidak ada di $validatedData tidak akan diubah.
        }

        // 'status' dari request adalah '0' atau '1', akan di-cast jadi boolean oleh Model
        // $validatedData['status'] = $request->input('status') === '1'; // Sudah ditangani $casts

        try {
            $property->update($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil diperbarui.',
                'property' => $property->fresh() // Mengembalikan data properti yang sudah fresh dari DB
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat update properti: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui data properti.', 'errors' => ['general' => [$e->getMessage()]]], 500);
        }
    }

    /**
     * Menghapus data properti.
     * Rute: DELETE /data-master/properti/{id}
     */
    public function destroy($id)
    {
        try {
            $property = UserProperty::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika request via AJAX, kirim JSON. Jika tidak, redirect dengan error.
            if (request()->ajax()) {
                return response()->json(['message' => 'Properti tidak ditemukan.'], 404);
            }
            return redirect()->route('data-master.properti.index')
                             ->with('error_delete', 'Properti tidak ditemukan.'); // Sesuai yang dicek Alpine
        }

        // Hapus gambar terkait dari storage sebelum menghapus record dari database
        if ($property->image && is_array($property->image)) {
            foreach ($property->image as $imageUrl) {
                $publicPath = parse_url($imageUrl, PHP_URL_PATH);
                $storagePath = 'public' . str_replace(Storage::url(''), '', $publicPath);
                if (Storage::exists($storagePath)) {
                    Storage::delete($storagePath);
                } else {
                    Log::warning("Saat destroy, file gambar tidak ditemukan untuk dihapus: " . $storagePath . " dari URL: " . $imageUrl);
                }
            }
        }

        try {
            $property->delete();
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data properti berhasil dihapus.']);
            }
            return redirect()->route('data-master.properti.index')
                             ->with('success', 'Data properti berhasil dihapus.'); // Pesan sukses standar
        } catch (\Exception $e) {
            Log::error('Error saat hapus properti: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['message' => 'Gagal menghapus data properti.', 'deleteError' => $e->getMessage()], 500); // Kirim 'deleteError'
            }
            return redirect()->route('data-master.properti.index')
                             ->with('error_delete', 'Gagal menghapus data properti.'); // Sesuai yang dicek Alpine
        }
    }
}