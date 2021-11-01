<?php


namespace App;


class UploadUtil
{
    public static function saveFileToStorage ($file, $pathFolder, $prefixName = null) {
        $storagePath = 'public' . DIRECTORY_SEPARATOR . $pathFolder;

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        if (!empty($prefixName)) {
            $filename = $prefixName . '.' .$filename;
        }
        $file->storeAs($storagePath, $filename);
        return "/storage/$pathFolder/$filename";
    }
}
