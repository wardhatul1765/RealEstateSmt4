<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class APIPropertyController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $user = auth()->user(); // pastikan middleware sanctum sudah aktif

        $validated = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'address' => 'required|string',
            'user_id' => 'required|string', // sesuaikan dengan relasi
            // validasi lainnya
        ]);

        $property = Property::create([
            'title' => $validated['title'],
            'description' => $request->description,
            'price' => $validated['price'],
            'bedrooms' => (int) $request->bedrooms,
            'bathrooms' => (int) $request->bathrooms,
            'sizeMin' => (float) $request->sizeMin,
            'furnishing' => $request->furnishing,
            'address' => $validated['address'],
            'status' => 'pendingVerification', // default
            'user_id' => $user->_id, // <-- kunci penghubung
            // Upload image logic...
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully',
            'data' => $property,
        ]);
    }
}