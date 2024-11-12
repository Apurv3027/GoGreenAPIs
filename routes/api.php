<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIs\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\WishlistController;

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

// Banners
Route::post('/upload-banner-image', [BannerController::class, 'uploadImage']); // Upload Banner Image
Route::post('/add-banner', [BannerController::class, 'store']); // Add Banner
Route::get('/banners', [BannerController::class, 'getAllBanners']); // Get All Banners
Route::post('/banners/{id}', [BannerController::class, 'update']); // Update Banner
Route::delete('/banners/{id}', [BannerController::class, 'destroy']); // Delete Banner

// Category
Route::post('/upload-category-image', [CategoryController::class, 'uploadImage']); // Upload Category Image
Route::post('/add-category', [CategoryController::class, 'store']); // Add Category
Route::get('/categories', [CategoryController::class, 'getAllCategories']); // Get All Category
Route::post('/categories/{id}', [CategoryController::class, 'update']); // Update Category
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete Category
Route::get('/categories/{categoryId}/products', [CategoryController::class, 'getProductsByCategory']); // Get Product Category Wise

// Product
Route::post('/upload-product-image', [ProductController::class, 'uploadImage']); // Upload Product Image
Route::post('/add-product', [ProductController::class, 'store']); // Add Product
Route::get('/products', [ProductController::class, 'getAllProducts']); // Get All Product
Route::post('/products/{id}', [ProductController::class, 'update']); // Update Product
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete Product
Route::get('/products/{id}', [ProductController::class, 'getProductById']);

// Users
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// Update User Profile
Route::post('/users/{id}', [UserController::class, 'update']);

// User Address
Route::post('/user/{id}/address', [AddressController::class, 'store']);
Route::get('/user/{id}/addresses', [AddressController::class, 'show']);
Route::get('/user/{id}/get-selected-address', [AddressController::class, 'getSelectedAddress']);
Route::post('/user/{id}/select-address', [AddressController::class, 'selectAddress']);
Route::post('/user/{id}/deselect-address', [AddressController::class, 'deselectAddress']);

// Add to Cart
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart/{user_id}', [CartController::class, 'fetchCartByUserId']);
Route::delete('/cart', [CartController::class, 'deleteFromCart']);

// Orders
Route::post('/create-orders', [OrdersController::class, 'store']);
Route::get('/orders', [OrdersController::class, 'index']);
Route::get('/orders/totalsales', [OrdersController::class, 'getTotalSales']);
Route::get('/orders/{userId}', [OrdersController::class, 'show']);

// Wishlist
Route::post('/wishlist/add/{user_id}', [WishlistController::class, 'addToWishlist']);
Route::post('/wishlist/remove/{user_id}', [WishlistController::class, 'removeFromWishlist']);
Route::get('/wishlist/view/{user_id}', [WishlistController::class, 'viewWishlist']);
