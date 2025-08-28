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

Route::get('/', [HomeController::class, 'index'])->name('root');
Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::namespace('App\Http\Controllers')->group(function () {
    Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Blog Management
        Route::resource('blogs', BlogController::class);
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
