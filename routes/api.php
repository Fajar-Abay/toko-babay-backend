<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);


Route::post('/products', [ProductController::class, 'store'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
    Route::delete('/cart/remove', [CartController::class, 'removeFromCart']);


    Route::get('/orders', [OrderController::class, 'listOrders']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']); // untuk admin/user ganti status
});

Route::get('/products', [ProductController::class, 'index']);
