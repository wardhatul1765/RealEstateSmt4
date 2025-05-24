<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator; // Untuk validasi AJAX

class DataMasterController extends Controller
{
    /**
     * Menampilkan halaman daftar master properti.
     */
    public function propertiIndex(Request $request)
    {
        $query = Property::query()->latest('addedOn'); // Urutkan berdasarkan tanggal terbaru

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('displayAddress', 'like', '%' . $search . '%');
            });
        }

        $dataProperty = $query->paginate(15); // Sesuaikan jumlah item per halaman jika perlu
        
        // Pastikan view yang dipanggil sesuai dengan path file blade Anda
        return view('data_master.index', compact('dataProperty'));
    }

    /**
     * Mengambil data properti untuk form edit (dipanggil via AJAX).
     */
    public function getPropertyEditData($id)
    {
        $property = Property::findOrFail($id);
        // $property sudah otomatis di-cast oleh model, termasuk 'addedOn' ke Carbon.
        // Jika frontend butuh format string tertentu untuk tanggal, bisa disesuaikan di sini
        // atau serahkan ke JavaScript untuk memformatnya.
        // Contoh: $property->addedOn_formatted = $property->addedOn->toDateString();
        return response()->json($property);
    }

    /**
     * Menyimpan properti baru ke database (dipanggil via AJAX).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'displayAddress' => 'nullable|string|max:1000',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'sizeMin' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:100',
            'furnishing' => 'nullable|string|in:Yes,No,Partly',
            'verified' => 'required|in:0,1', // Dari checkbox akan jadi '0' atau '1'
            'addedOn' => 'required|date_format:Y-m-d',
            'view_type' => 'nullable|string|max:100',
            'keyword_flags' => 'nullable|string|max:100', // Diharapkan sudah array dari JS
            'keyword_flags.*' => 'nullable|string|max:100', // Validasi tiap item di array
            'title_keyword' => 'nullable|integer',
            'listing_age_category' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['verified'] = ($validatedData['verified'] == '1'); // Konversi ke boolean
        
        // Pastikan addedOn di-parse dengan benar jika perlu, atau biarkan $casts yang urus
        // $casts 'datetime' akan handle string YYYY-MM-DD
        // $validatedData['addedOn'] = Carbon::parse($validatedData['addedOn']); // Sudah dihandle $casts

        // Jika keyword_flags datang dari input teks 'keyword_flags_string' yang dipisah koma:
        // Ini seharusnya sudah dihandle di JS sebelum submit, tapi sebagai fallback:
        // if ($request->has('keyword_flags_string') && is_string($request->keyword_flags_string)) {
        //    $flags = array_map('trim', explode(',', $request->keyword_flags_string));
        //    $validatedData['keyword_flags'] = array_filter($flags); // Hapus string kosong
        // } elseif (!isset($validatedData['keyword_flags']) || !is_array($validatedData['keyword_flags'])) {
        //    $validatedData['keyword_flags'] = []; // Default array kosong jika tidak valid
        // }


        $property = Property::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data properti berhasil ditambahkan.',
            'property' => $property
        ], 201); // Status 201 Created
    }

    /**
     * Memperbarui data properti (dipanggil via AJAX).
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'displayAddress' => 'nullable|string|max:1000',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'sizeMin' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:100',
            'furnishing' => 'nullable|string|in:Yes,No,Partly',
            'verified' => 'required|in:0,1',
            'addedOn' => 'required|date_format:Y-m-d',
            'view_type' => 'nullable|string|max:255', // HARUS STRING
            'keyword_flags' => 'nullable|string|max:100', // HARUS STRING
            'keyword_flags.*' => 'nullable|string|max:100',
            'title_keyword' => 'nullable|integer',
            'listing_age_category' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['verified'] = ($validatedData['verified'] == '1');
        // $validatedData['addedOn'] = Carbon::parse($validatedData['addedOn']); // Sudah dihandle $casts

        // Handle keyword_flags seperti di store jika perlu konversi dari string
        // if ($request->has('keyword_flags_string') && is_string($request->keyword_flags_string)) {
        //    $flags = array_map('trim', explode(',', $request->keyword_flags_string));
        //    $validatedData['keyword_flags'] = array_filter($flags);
        // } elseif (!isset($validatedData['keyword_flags']) || !is_array($validatedData['keyword_flags'])) {
        //    $validatedData['keyword_flags'] = $property->keyword_flags ?? []; // Pertahankan nilai lama jika input baru tidak valid
        // }

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
        $property = Property::findOrFail($id);
        $property->delete();

        // Untuk request non-AJAX, redirect dengan pesan sukses
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data properti berhasil dihapus.']);
        }

        return redirect()->route('data-master.properti.index') // Pastikan nama route ini benar
                         ->with('success', 'Data properti berhasil dihapus.');
    }
}