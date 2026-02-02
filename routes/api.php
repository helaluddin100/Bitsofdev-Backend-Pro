<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PricingController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\QAPairController;

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

// About API Routes
Route::get('/about', [AboutController::class, 'index']);
Route::get('/about/all', [AboutController::class, 'all']);
Route::get('/about/values', [AboutController::class, 'values']);
Route::get('/about/processes', [AboutController::class, 'processes']);
Route::get('/about/team', [AboutController::class, 'team']);

// Blog API Routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/featured', [BlogController::class, 'featured']);
Route::get('/blogs/categories', [BlogController::class, 'categories']);
Route::get('/blogs/debug', [BlogController::class, 'debug']);
Route::get('/blogs/debug/{slug}', [BlogController::class, 'debug']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);

// Project API Routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/featured', [ProjectController::class, 'featured']);
Route::get('/projects/status/{status}', [ProjectController::class, 'byStatus']);
Route::get('/projects/technologies', [ProjectController::class, 'technologies']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Product API Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/status/{status}', [ProductController::class, 'byStatus']);
Route::get('/products/technologies', [ProductController::class, 'technologies']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Team API Routes
Route::get('/team', [TeamController::class, 'index']);
Route::get('/team/featured', [TeamController::class, 'featured']);
Route::get('/team/{id}', [TeamController::class, 'show']);

// Pricing API Routes
Route::get('/pricing', [PricingController::class, 'index']);
Route::get('/pricing/popular', [PricingController::class, 'popular']);
Route::get('/pricing/cycle/{cycle}', [PricingController::class, 'byCycle']);
Route::get('/pricing/{slug}', [PricingController::class, 'show']);

// Visitor Analytics API Routes
Route::middleware('visitor.tracking')->group(function () {
    Route::post('/track-visitor', [VisitorController::class, 'store']);
    Route::post('/update-visitor-exit', [VisitorController::class, 'updateExit']);
});

// Contact Form API Routes
Route::post('/contact', [ContactController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::get('/admin/contacts/{contact}', [ContactController::class, 'show']);
    Route::put('/admin/contacts/{contact}', [ContactController::class, 'update']);
    Route::delete('/admin/contacts/{contact}', [ContactController::class, 'destroy']);
    Route::get('/admin/contacts/statistics', [ContactController::class, 'statistics']);
});

// Testimonials API Routes
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/testimonials/featured', [TestimonialController::class, 'featured']);
Route::get('/testimonials/project-type/{projectType}', [TestimonialController::class, 'byProjectType']);
Route::get('/testimonials/statistics', [TestimonialController::class, 'statistics']);
Route::get('/testimonials/project-types', [TestimonialController::class, 'projectTypes']);
Route::get('/testimonials/{id}', [TestimonialController::class, 'show']);
Route::post('/testimonials', [TestimonialController::class, 'store']);

// Admin Testimonials API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('admin/testimonials', TestimonialController::class);
    Route::post('admin/testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus']);
    Route::post('admin/testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured']);
    Route::post('admin/testimonials/bulk-action', [TestimonialController::class, 'bulkAction']);
    Route::get('admin/testimonials/export', [TestimonialController::class, 'export']);
});

// AI Chatbot API Routes
Route::post('/chat/ai-response', [QAPairController::class, 'getAIResponse']);
Route::get('/chat/qa-pairs', [QAPairController::class, 'index']);

// AI Learning Routes
Route::get('/ai/learning-stats', [QAPairController::class, 'getAILearningStats']);
Route::post('/ai/activate-learned', [QAPairController::class, 'activateLearnedResponses']);
Route::get('/ai/status', [QAPairController::class, 'checkAIStatus']);

// AI Settings Routes
Route::get('/ai/settings', [App\Http\Controllers\AISettingsController::class, 'getSettings']);
Route::post('/ai/settings', [App\Http\Controllers\AISettingsController::class, 'updateSettings']);
Route::post('/ai/switch-provider', [App\Http\Controllers\AISettingsController::class, 'switchProvider']);
Route::post('/ai/toggle-training', [App\Http\Controllers\AISettingsController::class, 'toggleTrainingMode']);
Route::post('/ai/toggle-static', [App\Http\Controllers\AISettingsController::class, 'toggleStaticResponses']);

// Conversation History Routes
Route::get('/conversation/history/{sessionId}', [QAPairController::class, 'getConversationHistory']);
Route::delete('/conversation/clear/{sessionId}', [QAPairController::class, 'clearConversationHistory']);


// Admin Q&A Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('admin/qa-pairs', QAPairController::class);
    Route::get('/admin/qa-pairs/statistics', [QAPairController::class, 'statistics']);
});
