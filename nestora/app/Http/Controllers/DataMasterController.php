<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class DataMasterController extends Controller
{
    /**
     * Menampilkan halaman daftar master properti.
     * Route: data-master.properti.index
     */
    public function propertiIndex(Request $request)
    {
        $query = Property::latest('addedOn'); // Urutkan berdasarkan tanggal terbaru

        // Pencarian berdasarkan judul atau alamat
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('displayAddress', 'like', '%' . $request->search . '%');
            });
        }

        $dataProperty = $query->paginate(15);
        return view('data_master.index', compact('dataProperty'));
    }

    /**
     * Menampilkan form untuk menambah data master properti baru.
     * Route: data-master.properti.create
     */
    public function create()
    {
        return view('data_master.create');
    }

    /**
     * Menyimpan properti baru ke database.
     * Route: data-master.properti.store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'displayAddress' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'sizeMin' => 'nullable|integer|min:0',
            'type' => 'required|string',
            'furnishing' => 'required|string|in:Yes,No',
            // 'verified' sekarang bisa '1' atau '0' dari form AJAX
            'verified' => 'required|in:0,1',
        ]);

        $validated['verified'] = $request->verified == '1'; // Konversi ke boolean
        $validated['addedOn'] = now();

        $property = Property::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil ditambahkan.',
                'property' => $property // Kirim data properti yang baru dibuat jika perlu di frontend
            ]);
        }

        return redirect()->route('data-master.properti.index')
            ->with('success', 'Data properti berhasil ditambahkan.');
    }

    public function getPropertyEditData($id)
    {
        $property = Property::findOrFail($id);
        // Anda mungkin perlu menyesuaikan format data agar sesuai dengan kebutuhan frontend
        // misalnya, konversi boolean ke '1'/'0' atau format tanggal jika perlu
        return response()->json($property);
    }

    /**
     * Menampilkan form edit properti.
     * Route: data-master.properti.edit
     */
    public function edit($id)
    {
        $property = Property::findOrFail($id);
        return view('data_master.edit', compact('property'));
    }

    /**
     * Memperbarui data properti.
     * Route: data-master.properti.update
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'displayAddress' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'sizeMin' => 'nullable|integer|min:0',
            'type' => 'required|string',
            'furnishing' => 'required|string|in:Yes,No',
            'verified' => 'required|in:0,1',
        ]);

        $validated['verified'] = $request->verified == '1';

        $property = Property::findOrFail($id);
        $property->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data properti berhasil diperbarui.',
                'property' => $property // Kirim data properti yang diperbarui
            ]);
        }

        return redirect()->route('data-master.properti.index')
            ->with('success', 'Data properti berhasil diperbarui.');
    }

    /**
     * Menghapus data properti.
     * Route: data-master.properti.destroy
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('data-master.properti.index')
            ->with('success', 'Data properti berhasil dihapus.');
    }
}
