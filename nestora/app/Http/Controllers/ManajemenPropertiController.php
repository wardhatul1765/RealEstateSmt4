<?php

namespace App\Http\Controllers;

use App\Models\UserProperty; // Menggunakan model Property
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Tambahkan ini jika ingin return response sederhana

class ManajemenPropertiController extends Controller
{
    /**
     * Menampilkan halaman daftar properti (untuk admin).
     * Route: manajemen-properti.index
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Memulai query
        $query = UserProperty::query();

        // Jika ada input pencarian, tambahkan kondisi where
        if ($searchTerm) {
            // Menggunakan preg_quote untuk escape karakter spesial regex
            $escapedSearchTerm = preg_quote($searchTerm, '/');

            $query->where(function ($q) use ($escapedSearchTerm, $searchTerm) {
                // Pencarian untuk field teks
                // Sesuaikan nama field dengan yang ada di Model Property
                $q->where('title', 'regexp', "/.*{$escapedSearchTerm}.*/i") // 'i' untuk case-insensitive
                    ->orWhere('Address', 'regexp', "/.*{$escapedSearchTerm}.*/i") // Sebelumnya 'displayAddress'
                    ->orWhere('propertyType', 'regexp', "/.*{$escapedSearchTerm}.*/i") // Sebelumnya 'type'
                    // ->orWhere('description', 'regexp', "/.*{$escapedSearchTerm}.*/i") // Dihapus karena 'description' tidak ada di model Property terbaru
                    ->orWhere('mainView', 'regexp', "/.*{$escapedSearchTerm}.*/i") // Sebelumnya 'view_type'
                    ->orWhere('furnishing', 'regexp', "/.*{$escapedSearchTerm}.*/i");

                // Jika searchTerm adalah numerik, coba cari juga di field numerik untuk kecocokan persis
                if (is_numeric($searchTerm)) {
                    $numericSearchTerm = (float) $searchTerm; // Konversi ke float untuk konsistensi
                    $q->orWhere('bedrooms', $numericSearchTerm)
                        ->orWhere('bathrooms', $numericSearchTerm)
                        ->orWhere('price', $numericSearchTerm)
                        ->orWhere('sizeMin', $numericSearchTerm);
                }
                // Catatan: Pencarian pada field tanggal (addedOn) biasanya memerlukan UI dan logika khusus
                // dan tidak ideal untuk pencarian teks umum seperti ini.
            });
        }

        // Mengurutkan berdasarkan yang terbaru dan melakukan paginasi
        // Penting: appends(request()->query()) agar parameter pencarian tetap ada di link pagination
        $dataProperty = $query->latest('addedOn')->paginate(15)->appends(request()->query());

        return view('manajemen-properti.index', compact('dataProperty', 'searchTerm'));
    }

    /**
     * Menampilkan halaman persetujuan iklan properti.
     * Akan menampilkan properti yang statusnya belum true (belum disetujui).
     * Route: manajemen-properti.persetujuan
     */
    public function persetujuan()
    {
        // Mengambil properti yang statusnya false (belum disetujui/pending)
        // Menggunakan model Property dan field 'status' (boolean)
        $properties = UserProperty::where('status', false)->paginate(10);

        return view('manajemen-properti.persetujuan', compact('properties'));
    }


    /**
     * Menampilkan form untuk menambah properti baru.
     * Route: manajemen-properti.create
     */
    public function create()
    {
        return view('manajemen-properti.create');
    }

    /**
     * Menyimpan properti baru ke database.
     * Route: manajemen-properti.store (Method: POST)
     */
    public function store(Request $request)
    {
        // Logika validasi dan penyimpanan perlu diimplementasikan
        // Contoh:
        // $validatedData = $request->validate([
        //     'title' => 'required|string|max:255',
        //     'Address' => 'required|string',
        //     'propertyType' => 'required|string',
        //     'price' => 'required|numeric',
        //     // tambahkan validasi lain sesuai kebutuhan
        // ]);
        // Property::create($validatedData);

        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Simpan belum diimplementasikan sepenuhnya.');
    }

    /**
     * Menampilkan detail properti (Opsional, tapi baik untuk CRUD).
     * Route: manajemen-properti.show
     */
    public function show(UserProperty $properti) // Menggunakan Route Model Binding
    {
        return view('manajemen-properti.show', compact('properti'));
    }


    /**
     * Menampilkan form untuk mengedit properti.
     * Route: manajemen-properti.edit
     */
    public function edit(UserProperty $properti) // Menggunakan Route Model Binding
    {
        return view('manajemen-properti.edit', compact('properti'));
    }

    /**
     * Memperbarui data properti di database.
     * Route: manajemen-properti.update (Method: PATCH/PUT)
     */
    public function update(Request $request, UserProperty $properti) // Menggunakan Route Model Binding
    {
        // Logika validasi dan update perlu diimplementasikan
        // Contoh:
        // $validatedData = $request->validate([
        //     'title' => 'required|string|max:255',
        //     // ... validasi lainnya
        // ]);
        // $properti->update($validatedData);

        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Update belum diimplementasikan sepenuhnya.');
    }

    /**
     * Menghapus properti dari database.
     * Route: manajemen-properti.destroy (Method: DELETE)
     */
    public function destroy(UserProperty $properti) // Menggunakan Route Model Binding
    {
        // $properti->delete();
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Hapus belum diimplementasikan sepenuhnya.');
    }

    /**
     * Mengubah status properti menjadi disetujui (status = true).
     */
    public function approve($id)
    {
        $property = UserProperty::findOrFail($id);
        $property->status = true; // Mengubah status menjadi true (disetujui)
        $property->save();

        return redirect()->route('manajemen-properti.persetujuan')->with('success', 'Properti berhasil disetujui.');
    }

    /**
     * Mengubah status properti menjadi ditolak (status = false).
     */
    public function reject($id)
    {
        $property = UserProperty::findOrFail($id);
        $property->status = false; // Mengubah status menjadi false (ditolak/pending)
        $property->save();

        return redirect()->route('manajemen-properti.persetujuan')->with('success', 'Properti berhasil ditolak.');
    }
}
