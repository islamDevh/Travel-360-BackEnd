<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait UploadFiles
{

    protected function uploadFile($file, $path)
    {        
        return $file->store($path, 'public');
    }

    protected function removeFile($path)
    {
        if (isset($path) && File::exists(storage_path(STORAGE_PATH . $path))) {
            File::delete(storage_path(STORAGE_PATH . $path));
        }
    }

    protected function getFile($path)
    {
        if (isset($path) && File::exists(storage_path(STORAGE_PATH . $path))) {
            return File::get(storage_path(STORAGE_PATH . $path));
        }
        return null;
    }
}
