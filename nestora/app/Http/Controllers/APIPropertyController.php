<?php

namespace App\Http\Controllers;

use App\Models\UserProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth');
    }

public function store(Request $request)
{
    Log::info('User Auth:', ['user' => auth()->user()]);
    Log::info('Request to store property received', $request->all());

    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No user found'], 401);
    }

    // Validasi utama + validasi file gambar
    $validated = $request->validate([
        'title' => 'required|string',
        'price' => 'required|numeric',
        'address' => 'required|string',
        'description' => 'required|string',
        'bedrooms' => 'required|integer',
        'bathrooms' => 'required|integer',
        'sizeMin' => 'required|numeric',
        'furnishing' => 'required|string',
        'image' => 'required|array',
        'image.*' => 'image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Proses upload file
    $uploadedFilenames = [];
    foreach ($request->file('image') as $file) {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/properties', $filename);
        $uploadedFilenames[] = url('storage/properties/' . $filename); // Simpan URL lengkap
    }


    // Buat properti
    $property = UserProperty::create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'price' => $validated['price'],
        'bedrooms' => (int) $validated['bedrooms'],
        'bathrooms' => (int) $validated['bathrooms'],
        'sizeMin' => (float) $validated['sizeMin'],
        'furnishing' => $validated['furnishing'],
        'address' => $validated['address'],
        'status' => 'pendingVerification',
        'user_id' => $user->_id,
        'image' => $uploadedFilenames, // ini adalah array
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Property created successfully with images',
        'data' => $property,
    ]);
}
}