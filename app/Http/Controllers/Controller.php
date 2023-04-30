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

        $image = Image::make($filePath)
            ->widen(1200, function ($constraint) {
                $constraint->upsize();
            })
            ->heighten(630, function ($constraint) {
                $constraint->upsize();
            })
            ->encode('jpg', 80);

        file_put_contents($imagePath, $image);

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
