<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\TemplatesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PeopleController;
use App\Http\Controllers\Api\ProductContentController;
use App\Http\Controllers\Api\ProductSectionController;
use App\Http\Controllers\Api\CertificateTemplateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/'], function ($router) {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('update', [AuthController::class, 'update']);

        Route::post('user', [AuthController::class, 'user']);

        Route::post('change_pass', [AuthController::class, 'changePassword']);
    });


    //PAGE
    Route::post('/project/page/{user_id}/{project}', [ProjectsController::class, 'savePage']);
    Route::get('/project/page/{user_id}/{project}', [ProjectsController::class, 'savePage']);



    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResources([
            'products' => ProductController::class,
            'podcasts' => PodcastController::class,
            'transactions' => TransactionController::class,
            'people' => PeopleController::class,
        ]);
        Route::post('products/{productId}/detail', [ProductController::class, 'detail']);

        Route::get('products/{productId}/sections', [ProductSectionController::class, 'index']);
        Route::apiResources(['sections' => ProductSectionController::class],['except' => ['index']]);

        Route::get('products/{productId}/pricing', [ProductController::class, 'pricingIndex']);
        Route::post('products/{productId}/pricing', [ProductController::class, 'pricingStore']);

        Route::get('products/{productId}/certificate', [ProductController::class, 'certificateShow']);
        Route::post('products/{productId}/certificate', [ProductController::class, 'certificateStore']);

        Route::get('products/{productId}/contents', [ProductContentController::class, 'index']);
        Route::apiResources(['contents' => ProductContentController::class],['except' => ['index']]);
        Route::apiResources(['pricing'  => ProductContentController::class],['except' => ['store', 'index']]);

        Route::middleware('role:admin|super-admin')->group(function () {
            Route::apiResources(['certificate-templates' => CertificateTemplateController::class]);
        });


        //BUILDER
        Route::apiResources(['projects' => ProjectsController::class]);
        Route::get('templates', [TemplatesController::class, 'index']);


    });

});
