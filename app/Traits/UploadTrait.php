<?php


namespace App\traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function uploadOne(UploadedFile $uploadedFile, $folder = 'images', $disk = 'public')
    {
        $file = Storage::disk($disk)->put($folder, $uploadedFile);
        $path = Storage::disk($disk)->url($file);
        return $path;
    }

    public function deleteOne($file, $folder = 'images/', $disk = 'public') {
        $oldFileName = array_reverse(explode('/', $file))[0];
        $filePath = $folder . $oldFileName;
        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
    }
}
