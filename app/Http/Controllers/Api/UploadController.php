<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|max:5120|mimes:jpeg,png,webp,gif',
        ]);

        $file = $request->file('file');
        if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'BAD_REQUEST', 'message' => 'No valid file uploaded']
            ], 400);
        }

        try {
            $imageUrl = StorageService::uploadFile($file);
            return response()->json(['success' => true, 'url' => $imageUrl]);
        } catch (\Throwable $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'SERVER_ERROR', 'message' => 'Failed to upload file']
            ], 500);
        }
    }
}
