<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\VisitorDataController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'redirectToLogin']);

Route::get('/clear-cache', [HomeController::class, 'clearCache']);


Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::namespace('App\Http\Controllers')->group(function () {
    Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Blog Management
        Route::resource('blogs', BlogController::class);
        Route::post('blogs/seo-suggestions', [BlogController::class, 'getSeoSuggestions'])->name('blogs.seo-suggestions');
        Route::get('blogs/test-seo', [BlogController::class, 'testSeo'])->name('blogs.test-seo');
        Route::get('blogs/test-binding/{id}', [BlogController::class, 'testBinding'])->name('blogs.test-binding');
        Route::get('blogs/test-controller', [BlogController::class, 'testController'])->name('blogs.test-controller');
        Route::get('blogs/debug', [BlogController::class, 'debugBlogs'])->name('blogs.debug');
        Route::get('blogs/test-auth', [BlogController::class, 'testAuth'])->name('blogs.test-auth');
        Route::resource('categories', CategoryController::class);

        // Project Management
        Route::resource('projects', ProjectController::class);

        // Product Management
        Route::resource('products', ProductController::class);

        // Team Management
        Route::resource('teams', TeamController::class);

        // Pricing Management
        Route::resource('pricing', PricingController::class);

        // About Management
        Route::get('about', [AboutController::class, 'index'])->name('about.index');
        Route::get('about/edit', [AboutController::class, 'edit'])->name('about.edit');
        Route::post('about/update', [AboutController::class, 'update'])->name('about.update');
        Route::post('about/values', [AboutController::class, 'storeValue'])->name('about.values.store');
        Route::post('about/values/{value}', [AboutController::class, 'updateValue'])->name('about.values.update');
        Route::delete('about/values/{value}', [AboutController::class, 'destroyValue'])->name('about.values.destroy');
        Route::post('about/processes', [AboutController::class, 'storeProcess'])->name('about.processes.store');
        Route::post('about/processes/{process}', [AboutController::class, 'updateProcess'])->name('about.processes.update');
        Route::delete('about/processes/{process}', [AboutController::class, 'destroyProcess'])->name('about.processes.destroy');

        // Analytics Management
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');
        Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
        Route::get('analytics/test', [AnalyticsController::class, 'test'])->name('analytics.test');

        // Visitor Data Management
        Route::get('visitors', [VisitorDataController::class, 'index'])->name('visitors.index');
        Route::get('visitors/{visitor}', [VisitorDataController::class, 'show'])->name('visitors.show');
        Route::delete('visitors/{visitor}', [VisitorDataController::class, 'destroy'])->name('visitors.destroy');
        Route::post('visitors/bulk-delete', [VisitorDataController::class, 'bulkDelete'])->name('visitors.bulk-delete');
        Route::get('visitors/export', [VisitorDataController::class, 'export'])->name('visitors.export');

        // Contact Management
        Route::resource('contacts', ContactController::class);
        Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');

        // Testimonial Management
        Route::resource('testimonials', TestimonialController::class);
        Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');
        Route::post('testimonials/{testimonial}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
        Route::post('testimonials/bulk-action', [TestimonialController::class, 'bulkAction'])->name('testimonials.bulk-action');
        Route::get('testimonials/export', [TestimonialController::class, 'export'])->name('testimonials.export');
    });

    // AI Chatbot Admin Routes
    Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
        Route::get('/ai-dashboard', [AdminController::class, 'dashboard'])->name('ai-dashboard');
        Route::get('/qa-management', [AdminController::class, 'qaManagement'])->name('qa-management');
        Route::post('/qa-store', [AdminController::class, 'storeQA'])->name('qa-store');
        Route::post('/qa-update/{id}', [AdminController::class, 'updateQA'])->name('qa-update');
        Route::delete('/qa-delete/{id}', [AdminController::class, 'deleteQA'])->name('qa-delete');
        Route::post('/qa-toggle/{id}', [AdminController::class, 'toggleStatus'])->name('qa-toggle');
        Route::post('/test-ai-response', [AdminController::class, 'testAIResponse'])->name('test-ai-response');

        // Visitor Questions Management
        Route::get('/visitor-questions', [AdminController::class, 'visitorQuestions'])->name('visitor-questions');
        Route::post('/answer-visitor-question/{id}', [AdminController::class, 'answerVisitorQuestion'])->name('answer-visitor-question');
        Route::post('/mark-converted/{id}', [AdminController::class, 'markAsConverted'])->name('mark-converted');
        Route::get('/visitor-questions-stats', [AdminController::class, 'getVisitorQuestionsStats'])->name('visitor-questions-stats');

        // Quick Answers Management
        Route::get('/quick-answers', [AdminController::class, 'quickAnswers'])->name('quick-answers');
        Route::get('/test-website-data', [AdminController::class, 'testWebsiteData'])->name('test-website-data');

        // AI Control Dashboard
        Route::get('/ai-control', [App\Http\Controllers\Admin\AIControlController::class, 'index'])->name('ai-control');
        Route::post('/ai-control/update-settings', [App\Http\Controllers\Admin\AIControlController::class, 'updateSettings'])->name('ai.update-settings');
        Route::post('/ai-control/switch-provider', [App\Http\Controllers\Admin\AIControlController::class, 'switchProvider'])->name('ai.switch-provider');
        Route::post('/ai-control/toggle-training', [App\Http\Controllers\Admin\AIControlController::class, 'toggleTrainingMode'])->name('ai.toggle-training');
        Route::post('/ai-control/toggle-static', [App\Http\Controllers\Admin\AIControlController::class, 'toggleStaticResponses'])->name('ai.toggle-static');
        Route::post('/ai-control/activate-learned', [App\Http\Controllers\Admin\AIControlController::class, 'activateLearned'])->name('ai.activate-learned');
    });
});

// Marketing Campaign Routes
require __DIR__ . '/marketing.php';

// ================================user AND ROUTE=============
Route::namespace('App\Http\Controllers')->group(
    function () {
        Route::group(['as' => 'user.', 'prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth', 'user']], function () {
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        });
    }
);
// ================================user AND ROUTE END=============
