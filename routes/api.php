<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PricingController;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::post('/auth/verify', [AuthController::class, 'verify']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/update/user', [UserController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Business types route
Route::get('country', [ShopController::class, 'countries']);
Route::get('business-type', [ShopController::class, 'businessTypes']);
Route::post('create-shop', [ShopController::class, 'store']);
Route::get('/user/{userId}/shops', [ShopController::class, 'getUserShops']);
Route::post('/update-shop/{shop}', [ShopController::class, 'update']);
Route::get('/shop/{id}', [ShopController::class, 'edit']);

// Blog API Routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/featured', [BlogController::class, 'featured']);
Route::get('/blogs/categories', [BlogController::class, 'categories']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);

// Project API Routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/featured', [ProjectController::class, 'featured']);
Route::get('/projects/status/{status}', [ProjectController::class, 'byStatus']);
Route::get('/projects/technologies', [ProjectController::class, 'technologies']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Team API Routes
Route::get('/team', [TeamController::class, 'index']);
Route::get('/team/featured', [TeamController::class, 'featured']);
Route::get('/team/{id}', [TeamController::class, 'show']);

// Pricing API Routes
Route::get('/pricing', [PricingController::class, 'index']);
Route::get('/pricing/popular', [PricingController::class, 'popular']);
Route::get('/pricing/cycle/{cycle}', [PricingController::class, 'byCycle']);
Route::get('/pricing/{slug}', [PricingController::class, 'show']);
