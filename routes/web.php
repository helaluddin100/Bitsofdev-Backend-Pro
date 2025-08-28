<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\PricingController;
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

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/clear-cache', function () {
    // Clear route cache
    Artisan::call('route:clear');

    // Optimize class loading
    Artisan::call('optimize');

    // Optimize configuration loading
    Artisan::call('config:cache');

    // Optimize views loading
    Artisan::call('view:cache');

    // Additional optimizations you may want to run

    return "Cache cleared and optimizations done successfully.";
});


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
        Route::get('blogs/test-auth', function () {
            return response()->json([
                'user' => auth()->user() ? auth()->user()->id : 'Not authenticated',
                'role' => auth()->user() ? auth()->user()->role : 'No role',
                'timestamp' => now()
            ]);
        })->name('blogs.test-auth');
        Route::resource('categories', CategoryController::class);

        // Project Management
        Route::resource('projects', ProjectController::class);

        // Team Management
        Route::resource('teams', TeamController::class);

        // Pricing Management
        Route::resource('pricing', PricingController::class);
    });
});

// ================================user AND ROUTE=============
Route::namespace('App\Http\Controllers')->group(
    function () {
        Route::group(['as' => 'user.', 'prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth', 'user']], function () {
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        });
    }
);
// ================================user AND ROUTE END=============
