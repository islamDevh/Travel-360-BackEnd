<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait UploadFiles
{

    protected function uploadFile($newFile, $path)
    {
        $file_name = uuid_create() . "." . $newFile->getClientOriginalExtension();
        $filePath  = $newFile->storeAs($path, $file_name, 'public');
        return $file_name;
    }

    protected function removeFile($path)
    {
        if (File::exists(storage_path(storagePath . $path))) {
            File::delete(storage_path(storagePath . $path));
        }
    }

    protected function getFile($path)
    {
        if (File::exists(storage_path(storagePath . $path))) {
            return File::get(storage_path(storagePath . $path));
        }
        return null;
    }
}
