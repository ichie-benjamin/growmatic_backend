<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/editor/{id}', [HomeController::class, 'editor'])->name('editor_url');


//
//Route::get('{path}', function () {
//    return view('index');
//})->where('path', '.*');

