<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::view('/upload-form', 'upload');
Route::get('/test-cloudinary', function () {
    return env('CLOUDINARY_URL');
});
Route::get('/debug-env', function () {
    return response()->json([
        'env' => $_ENV,
        'server' => $_SERVER,
    ]);
});