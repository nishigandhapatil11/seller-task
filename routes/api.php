<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;

Route::post('admin/login',[AuthController::class,'adminLogin']);
Route::post('seller/login',[AuthController::class,'sellerLogin']);

Route::middleware('auth:sanctum')->group(function(){

    Route::post('create-seller',[AdminController::class,'createSeller']);
    Route::get('seller-list',[AdminController::class,'sellerList']);

    Route::post('add-product',[ProductController::class,'addProduct']);
    Route::get('product-list',[ProductController::class,'productList']);
    Route::get('product-pdf/{id}',[ProductController::class,'productPDF']);
    Route::delete('delete-product/{id}',[ProductController::class,'deleteProduct']);
});