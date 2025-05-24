<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProperty; // Menggunakan model UserProperty sesuai kode Anda
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DataMasterController extends Controller
{
    /**
     * Menampilkan halaman daftar master properti.
     */
    public function propertiIndex(Request $request)
    {
        // Menggunakan 'created_at' atau 'addedOn' sesuai field yang ada di UserProperty dan diinginkan untuk sorting
        // Jika 'addedOn' tidak ada di UserProperty, gunakan 'created_at' atau field tanggal relevan lainnya.
        // Untuk contoh ini, kita asumsikan 'created_at' atau default model Eloquent jika 'addedOn' tidak ada.
        // Jika UserProperty punya 'addedOn', maka $query = UserProperty::query()->latest('addedOn'); sudah benar.
        // Jika tidak, mungkin $query = UserProperty::query()->latest(); atau latest('created_at')
        $query = UserProperty::query()->latest('created_at'); // Diasumsikan 'addedOn' mungkin tidak ada, ganti jika perlu

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('Address', 'like', '%' . $search . '%'); // Disesuaikan dari displayAddress ke address
            });
        }

        $dataProperty = $query->paginate(15);
        
        return view('data_master.index', compact('dataProperty'));
    }

    /**
     * Mengambil data properti untuk form edit (dipanggil via AJAX).
     */
    public function getPropertyEditData($id)
    {
        $property = UserProperty::findOrFail($id);
        // Jika UserProperty memiliki cast untuk 'addedOn', Carbon sudah otomatis.
        // Jika 'addedOn' tidak ada atau tidak di-cast, dan frontend butuh format:
        // if ($property->addedOn) {
        //    $property->addedOn_formatted = Carbon::parse($property->addedOn)->toDateString();
        // }
        return response()->json($property);
    }

    /**
     * Menyimpan properti baru ke database (dipanggil via AJAX).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'Address' => 'nullable|string|max:1000', // Disesuaikan dari displayAddress
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0', // Pastikan UserProperty cast price ke float/integer sesuai kebutuhan
            'sizeMin' => 'nullable|numeric|min:0',
            'propertyType' => 'nullable|string|max:100', // Disesuaikan dari type
            'furnishing' => 'nullable|string|in:Yes,No,Partly', // Model UserProperty $casts['furnishing'] => 'string'
            'status' => 'required|in:0,1', // Disesuaikan dari verified. Model UserProperty $casts['status'] => 'boolean'
            'addedOn' => 'nullable|date_format:Y-m-d', // Pastikan 'addedOn' ada di $fillable & $casts UserProperty jika ingin disimpan
            'mainView' => 'nullable|string|max:100', // Disesuaikan dari view_type
            'listing_age_category' => 'nullable|integer', // Sesuai UserProperty $fillable
            'description' => 'nullable|string', // Sesuai UserProperty $fillable
            'propertyLabel' => 'nullable|string|max:255', // Sesuai UserProperty $fillable
            'image' => 'nullable|array', // Sesuai UserProperty $fillable & $casts
            'image.*' => 'nullable|string', // Validasi untuk tiap item di array image jika image adalah array of strings (URL)
            'user_id' => 'nullable|string', // Sesuai UserProperty $fillable
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        // Konversi status (dari '0'/'1') ke boolean jika model UserProperty mengharapkan boolean
        if (isset($validatedData['status'])) {
            $validatedData['status'] = ($validatedData['status'] == '1');
        }
        
        // Jika 'addedOn' ada dan dikirim, pastikan model UserProperty dapat menanganinya (ada di $fillable dan $casts 'datetime')
        // Eloquent akan otomatis mem-parse string tanggal Y-m-d jika di-cast ke 'datetime'

        $property = UserProperty::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data properti berhasil ditambahkan.',
            'property' => $property
        ], 201);
    }

    /**
     * Memperbarui data properti (dipanggil via AJAX).
     */
    public function update(Request $request, $id)
    {
        $property = UserProperty::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'Address' => 'nullable|string|max:1000', // Disesuaikan
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'sizeMin' => 'nullable|numeric|min:0',
            'propertyType' => 'nullable|string|max:100', // Disesuaikan
            'furnishing' => 'nullable|string|in:Yes,No,Partly',
            'status' => 'required|in:0,1', // Disesuaikan
            'addedOn' => 'nullable|date_format:Y-m-d',
            'mainView' => 'nullable|string|max:255', // Disesuaikan
            'listing_age_category' => 'nullable|integer',
            'description' => 'nullable|string',
            'propertyLabel' => 'nullable|string|max:255',
            'image' => 'nullable|array',
            'image.*' => 'nullable|string',
            'user_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        if (isset($validatedData['status'])) {
            $validatedData['status'] = ($validatedData['status'] == '1');
        }

        $property->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data properti berhasil diperbarui.',
            'property' => $property
        ]);
    }

    /**
     * Menghapus data properti.
     */
    public function destroy($id)
    {
        $property = UserProperty::findOrFail($id);
        $property->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data properti berhasil dihapus.']);
        }

        // Pastikan nama route ini benar ('data-master.properti.index' atau nama route Anda)
        return redirect()->route('data-master.properti.index') 
                         ->with('success', 'Data properti berhasil dihapus.');
    }
}
