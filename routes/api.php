<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIs\AuthController;
use App\Http\Controllers\ProductsController;

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
