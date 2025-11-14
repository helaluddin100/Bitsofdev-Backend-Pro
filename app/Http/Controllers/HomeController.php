<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Redirect to login page
     */
    public function redirectToLogin()
    {
        return redirect('/login');
    }

    /**
     * Clear cache and optimize application
     */
    public function clearCache()
    {
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
    }
}
