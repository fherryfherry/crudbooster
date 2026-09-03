<?php

namespace CrudBooster\Components\Type\TinyMce\Function;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                "file"=> "required|image|mimes:jpeg,png,jpg,gif|max:2048",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/images');
            $url = Storage::url($path);

            return response()->json(['location' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
