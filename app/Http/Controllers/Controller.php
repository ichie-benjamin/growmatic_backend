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
        $htmlContent = file_get_contents($filePath);

        $image = Image::canvas(1200, 630, '#ffffff')
            ->text($htmlContent, 20, 20, function($font) {
                $font->size(16);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
            });

        $image->save($imagePath, 80);

        return response()->file($imagePath);

    }


    /**
     * Get template image path or default.
     *
     * @param string $name
     * @return string
     */

}
