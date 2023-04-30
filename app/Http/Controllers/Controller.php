<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

//    private $storage;
//
//    const DEFAULT_THUMBNAIL = 'default_project_thumbnail.png';

//    public function __construct()
//    {
//        $this->storage = Storage::disk('builder');
//    }

    public function successResponse($message, $data = [], $code = 200):JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if(!empty($data)){
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function errorResponse($error, $code = 404): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'error' => $error,
        ], $code);
    }

    public function generatePreviewImage($filePath, $imagePath)
    {

        // Load the HTML content of the file
        $htmlContent = file_get_contents($filePath);

        // Create a new image resource with a width of 1200px and a height of 630px
        $image = imagecreatetruecolor(1200, 630);

        // Set the background color of the image to white
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        $fontColor = imagecolorallocate($image, 0, 0, 0);

        $x = 0;
        $y = 16 * 2;

        $lines = explode("\n", wordwrap(strip_tags($htmlContent), 80, "\n"));

        foreach ($lines as $line) {
            imagettftext($image, 16, 0, $x, $y, $fontColor, 16, $line);
            $y += 16 * 1.5;
        }

        // Save the image as a JPEG file with a quality of 80
        imagejpeg($image, $imagePath, 80);

        // Destroy the image resource to free up memory
        imagedestroy($image);

        // Return the image file as a response
        return response()->file($imagePath);

    }

//    public function loadTemplates()
//    {
//        $storage = Storage::disk('builder');
//
//        $paths = $storage->directories('templates');
//
//        return collect($paths)->map(function($path) {
//            $name = basename($path);
//
//            $updatedAt = Storage::disk('builder')->exists("$path/index.html")
//                ? Carbon::createFromTimestamp(Storage::disk('builder')->lastModified("$path/index.html"))->toDateTimeString()
//                : Carbon::now();
//
//            return [
//                'name' => $name,
//                'updated_at' => $updatedAt,
////                'config' => $this->getTemplateConfig(basename($path)),
//                'thumbnail' => $this->getTemplateImagePath($name),
//                'thumbnail_url' => $this->getTemplateImagePath($name),
//            ];
//        });
//    }
//
//    public function loadTemplate($name)
//    {
//        $paths = Storage::disk('builder')->files("templates/$name");
//
//        $pages = collect($paths)->filter(function($path) {
//            return Str::contains($path, '.html');
//        })->map(function($path) use($name) {
//            return [
//                'name' => basename($path, '.html'),
//                'html' => $this->storage->get($path),
//            ];
//        })->values();
//
//        return [
//            'name' => $name,
////            'config' => $this->getTemplateConfig($name),
//            'thumbnail' => $this->getTemplateImagePath($name),
//            'pages' => $pages,
//        ];
//    }
//
//
//    public function templateExists($name)
//    {
//        return Storage::disk('builder')->exists("templates/$name");
//    }

    /**
     * Get template image path or default.
     *
     * @param string $name
     * @return string
     */
//    private function getTemplateImagePath($name)
//    {
//        $path = "templates/$name/thumbnail.png";
//
//        if (Storage::disk('builder')->exists($path)) {
//            return Storage::disk('builder')->url($path);
//        }
//
//        return base_path('builder')->url(self::DEFAULT_THUMBNAIL);
//    }


}
