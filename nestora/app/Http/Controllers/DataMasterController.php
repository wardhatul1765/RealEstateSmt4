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
    public function propertiIndex()
    {
        $dataProperty = Property::latest()->paginate(15);
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
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'price' => 'required|integer',
            'type' => 'required|string',
        ]);

        Property::create($validated);

        return redirect()->route('data-master.properti.index')
            ->with('success', 'Data properti berhasil ditambahkan.');
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
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'price' => 'required|integer',
            'type' => 'required|string',
        ]);

        $property = Property::findOrFail($id);
        $property->update($validated);

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
