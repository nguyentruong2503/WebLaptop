<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\Home_client;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductTypesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/upload', [UploadController    ::class, 'upload']);

//Quản lý loại sản phẩm
Route::apiResource('product_types', ProductTypesController::class);

//Giỏ hàng  trang chủ
Route::get('/products', [Home_client::class, 'getByLoai']);
Route::get('/products_mouse', [Home_client::class, 'getAccessory']);
Route::get('/laptops/{id}', [Home_client::class, 'getLaptopById']);
Route::get('/accessory/{id}', [Home_client::class, 'getAccessoryById']);

Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart/{userId}', [CartController::class, 'getCartByUser']);
Route::put('/cart/{cartId}', [CartController::class, 'updateCart']);
Route::delete('/cart/{cartId}', [CartController::class, 'deleteCart']);
Route::post('/orders', [OrderController::class, 'store']);

