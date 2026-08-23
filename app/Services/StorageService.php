<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class StorageService
{
    public static function uploadFile(UploadedFile $file): string
    {
        $provider = env('STORAGE_PROVIDER', 'local');

        if ($provider === 'database') {
            $mime = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            return "data:{$mime};base64,{$base64}";
        }

        // Default 'local' storage for shared hosting (MilesWeb)
        $uniqueSuffix = time() . '-' . rand(100000, 999999);
        $safeName = preg_replace('/[^a-zA-Z0-9.\-_]/', '', $file->getClientOriginalName());
        $filename = "{$uniqueSuffix}-{$safeName}";

        $uploadDir = public_path('uploads');
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $file->move($uploadDir, $filename);

        return "/uploads/{$filename}";
    }
}
