<?php


namespace App\Utils;


use Illuminate\Support\Facades\Storage;

class UploadUtil
{
    public static function saveFileToStorage ($file, $pathFolder, $prefixName = null) {
        $storagePath = 'public' . DIRECTORY_SEPARATOR . $pathFolder;

        $filename = $file->getClientOriginalName();
        if (!empty($prefixName)) {
            $filename = $prefixName . '.' .$filename;
        }
        $file->storeAs($storagePath, $filename);
        return "/storage/$pathFolder/$filename";
    }

    public static function saveBase64ImageToStorage($base64, $disk)
    {
        @list($type, $file_data) = explode(';', $base64);
        @list(, $file_data) = explode(',', $file_data);
        $imageName = $disk . '_' . time() .  '_'  . uniqid() . '.png';
        Storage::disk($disk)->put($imageName, base64_decode($file_data));
        return '/storage/' . $disk . '/' . $imageName;
    }
}
