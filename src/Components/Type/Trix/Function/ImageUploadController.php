<?php

namespace CrudBooster\Components\Type\Trix\Function;

use CrudBooster\Helpers\CbUploader;
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
            $path = CbUploader::uploadFromUploadedFile($request->file('file'));
            // Use helper to generate URL compatible with both local/public and S3/private disks
            $url = getStorageUrl($path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
