<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function fileUpload($file, $dirPath)
    {
        if ($file->isValid()) {
            $extension = $file->extension();
            $mimeType = $file->getClientMimeType();
            $filename = time().'-'.str_replace(' ', '-', $file->getClientOriginalName());
            $path = $file->storeAs("public/$dirPath", $filename);

            return [
                'name' => $filename,
                'mime_type' => $mimeType,
                'path' => $path,
                'extension' => $extension,
            ];
        }
        return [];
    }
}
