<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIImageController extends Controller
{
    public function upload(Request $request)
    {
        $urls = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('property_images', 'public');
            $urls[] = asset('storage/' . $path);
        }

        return response()->json(['urls' => $urls]);
    }
}
