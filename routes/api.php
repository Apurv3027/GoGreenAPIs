<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIs\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;

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

// Register User
Route::post('/register', [AuthController::class, 'registerUser']);

// Login User
Route::post('/login', [AuthController::class, 'loginUser']);

// Add Products
Route::post('/add-product', [ProductsController::class, 'store']);

// Get Single Product
Route::get('/products/{name}', [ProductsController::class, 'show']);

// Get All Products
Route::get('/products', [ProductsController::class, 'index']);


// Banners
Route::post('/upload-banner-image', [BannerController::class, 'uploadImage']);  // Upload Banner Image
Route::post('/add-banner', [BannerController::class, 'store']); // Add Banner
Route::get('/banners', [BannerController::class, 'getAllBanners']); // Get All Banners
Route::post('/banners/{id}', [BannerController::class, 'update']);   // Update Banner
Route::delete('/banners/{id}', [BannerController::class, 'destroy']); // Delete Banner

// Category
Route::post('/upload-category-image', [CategoryController::class, 'uploadImage']);  // Upload Category Image
Route::post('/add-category', [CategoryController::class, 'store']); // Add Category
Route::get('/categories', [CategoryController::class, 'getAllCategories']); // Get All Category
Route::post('/categories/{id}', [CategoryController::class, 'update']);   // Update Category
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete Category
