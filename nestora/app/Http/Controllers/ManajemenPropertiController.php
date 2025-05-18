<?php

namespace App\Http\Controllers;

use App\Models\Persetujuan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Property;
 // Tambahkan ini jika ingin return response sederhana

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
        $query = Property::query();

        // Jika ada input pencarian, tambahkan kondisi where
        if ($searchTerm) {
            // Menggunakan preg_quote untuk escape karakter spesial regex
            $escapedSearchTerm = preg_quote($searchTerm, '/');

            $query->where(function ($q) use ($escapedSearchTerm, $searchTerm) {
                // Pencarian untuk field teks
                $q->where('title', 'regexp', "/.*{$escapedSearchTerm}.*/i") // 'i' untuk case-insensitive
                  ->orWhere('displayAddress', 'regexp', "/.*{$escapedSearchTerm}.*/i")
                  ->orWhere('type', 'regexp', "/.*{$escapedSearchTerm}.*/i")
                  ->orWhere('description', 'regexp', "/.*{$escapedSearchTerm}.*/i") // Tetap mencari di deskripsi
                  ->orWhere('view_type', 'regexp', "/.*{$escapedSearchTerm}.*/i")
                  ->orWhere('furnishing', 'regexp', "/.*{$escapedSearchTerm}.*/i"); // Mencari berdasarkan furnishing (misal "YES" atau "NO")

                // Jika searchTerm adalah numerik, coba cari juga di field numerik untuk kecocokan persis
                if (is_numeric($searchTerm)) {
                    $numericSearchTerm = (float) $searchTerm; // Konversi ke float untuk konsistensi
                    $q->orWhere('bedrooms', $numericSearchTerm)
                      ->orWhere('bathrooms', $numericSearchTerm)
                      ->orWhere('price', $numericSearchTerm)
                      ->orWhere('sizeMin', $numericSearchTerm);
                }
                // Catatan: Pencarian pada field tanggal (addedOn) biasanya memerlukan UI dan logika khusus (misal, date range picker)
                // dan tidak ideal untuk pencarian teks umum seperti ini.
                // Field 'No' (nomor urut) adalah untuk tampilan dan tidak dicari di database.
            });
        }

        // Mengurutkan berdasarkan yang terbaru dan melakukan paginasi
        // Penting: appends(request()->query()) agar parameter pencarian tetap ada di link pagination
        $dataProperty = $query->latest('addedOn')->paginate(15)->appends(request()->query());

        return view('manajemen-properti.index', compact('dataProperty', 'searchTerm'));
    }

    /**
     * Menampilkan halaman persetujuan iklan properti.
     * Route: manajemen-properti.persetujuan
     */
    public function persetujuan()
{
    $properties = Persetujuan::where('verified', false)->paginate(10);


    return view('manajemen-properti.persetujuan', compact('properties'));
}


    /**
     * Menampilkan form untuk menambah properti baru.
     * Route: manajemen-properti.create
     */
    public function create()
    {
        // Kosongkan dulu atau beri placeholder
        return view('manajemen-properti.create');
        //  return response("Halaman Form Tambah Properti"); // Placeholder
    }

    /**
     * Menyimpan properti baru ke database.
     * Route: manajemen-properti.store (Method: POST)
     */
    public function store(Request $request)
    {
        // Kosongkan dulu
        // Logika validasi dan penyimpanan ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Simpan belum diimplementasikan.');
        // return response("Proses Simpan Properti (Belum Implementasi)"); // Placeholder
    }

    /**
     * Menampilkan detail properti (Opsional, tapi baik untuk CRUD).
     * Route: manajemen-properti.show
     */
    public function show($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        return view('manajemen-properti.show');
        // return response("Halaman Detail Properti ID: " . $properti); // Placeholder
    }


    /**
     * Menampilkan form untuk mengedit properti.
     * Route: manajemen-properti.edit
     */
    public function edit($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        return view('manajemen-properti.edit');
        //  return response("Halaman Form Edit Properti ID: " . $properti); // Placeholder
    }

    /**
     * Memperbarui data properti di database.
     * Route: manajemen-properti.update (Method: PATCH/PUT)
     */
    public function update(Request $request, $properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        // Logika validasi dan update ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Update belum diimplementasikan.');
        //  return response("Proses Update Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }

    /**
     * Menghapus properti dari database.
     * Route: manajemen-properti.destroy (Method: DELETE)
     */
    public function destroy($properti) // Nama parameter {properti} dari route
    {
        // Kosongkan dulu
        // Logika delete ditunda

        // Redirect sementara atau beri respons
        return redirect()->route('manajemen-properti.index')->with('info', 'Fungsi Hapus belum diimplementasikan.');
        // return response("Proses Hapus Properti ID: " . $properti . " (Belum Implementasi)"); // Placeholder
    }

     /**
     * Mengubah status persetujuan properti (Contoh action tambahan).
     */
    public function approve($id)
{
    $property = Property::findOrFail($id);
    $property->status = 'approved';
    $property->save();

    return redirect()->route('manajemen-properti.persetujuan')->with('success', 'Iklan berhasil disetujui.');
}

public function reject($id)
{
    $property = Property::findOrFail($id);
    $property->status = 'rejected';
    $property->save();

    return redirect()->route('manajemen-properti.persetujuan')->with('success', 'Iklan berhasil ditolak.');
}
}
