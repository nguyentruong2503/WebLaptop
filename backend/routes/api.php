<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\Home_client;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Payment_OrderController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\OrderAdminController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductTypesController;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandsController;


use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ThongkeController;



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


Route::post('/upload', [UploadController::class, 'upload']);
// //Register
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/payment/vnpay-return', [Payment_OrderController::class, 'vnpayReturn']);

// Login+Me+refresh token nhé ae
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('user', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

//Users
Route::middleware('jwt.auth')->group(function () {
//đơn hàng client
    Route::get('/orders/user', [OrderController::class, 'getOrderByUser']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetailByOrderId']); 
    Route::put('/orders/{id}', [OrderController::class, 'updateStatus']);
//Get danh sách sản phẩm
    Route::get('/products_client', [Home_client::class, 'getByLoai']);
    Route::get('/products_mouse', [Home_client::class, 'getAccessory']);
    Route::get('/laptops/{id}', [Home_client::class, 'getLaptopById']);
    Route::get('/accessory/{id}', [Home_client::class, 'getAccessoryById']);
//Thao tác với giỏ hàng
    Route::get('/cart', [CartController::class, 'getCartByUser']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/{cartId}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{cartId}', [CartController::class, 'deleteCart']);
    Route::post('/payment/cod', [Payment_OrderController::class, 'cod']);
    Route::post('/payment/vnpay', [Payment_OrderController::class, 'vnpay']);
//Tính phí ship   
//  Route::post('/shipping/fee', [ShippingController::class, 'calculateFee']);
//Cập nhật thông tin cá nhân
    Route::put('userClient', [AuthController::class, 'updateMe']);
});


//admin

Route::middleware(['jwt.auth','role:admin'])->group(function () {
//Quản lý người dùng
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
//Loại sản phẩm
    Route::get('product_types', [ProductTypesController::class, 'index']);
    Route::post('product_types', [ProductTypesController::class, 'store']);
    Route::get('product_types/{id}', [ProductTypesController::class, 'show']);
    Route::put('product_types/{id}', [ProductTypesController::class, 'update']);
    Route::delete('product_types/{id}', [ProductTypesController::class, 'destroy']);
//Sản Phẩm
    Route::apiResource('products', ProductController::class);
//Brands
    Route::get('brands', [BrandsController::class, 'index']);
    Route::post('brands', [BrandsController::class, 'store']);
    Route::get('brands/{id}', [BrandsController::class, 'show']);
    Route::put('brands/{id}', [BrandsController::class, 'update']);
    Route::delete('brands/{id}', [BrandsController::class, 'destroy']);
//OrderAdmin
    Route::get('/admin/orders', [OrderAdminController::class, 'index']);
    Route::put('/admin/orders/{id}/status', [OrderAdminController::class, 'updateStatus']);
//thongke
    Route::get('/thongke', [ThongkeController::class, 'dashboard']);
});

