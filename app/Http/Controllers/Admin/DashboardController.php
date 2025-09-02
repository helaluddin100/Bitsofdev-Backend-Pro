<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // User Statistics
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersThisWeek = User::where('created_at', '>=', $thisWeek)->count();
        $newUsersThisMonth = User::where('created_at', '>=', $thisMonth)->count();

        // Project Statistics
        $totalProjects = Project::count();
        $activeProjects = Project::where('is_active', true)->count();
        $featuredProjects = Project::where('is_featured', true)->count();
        $projectsThisMonth = Project::where('created_at', '>=', $thisMonth)->count();

        // Blog Statistics
        $totalBlogs = Blog::count();
        $publishedBlogs = Blog::where('status', 'published')->count();
        $featuredBlogs = Blog::where('is_featured', true)->count();
        $blogsThisMonth = Blog::where('created_at', '>=', $thisMonth)->count();

        // Contact Statistics
        $totalContacts = Contact::count();
        $newContacts = Contact::where('status', 'new')->count();
        $repliedContacts = Contact::where('status', 'replied')->count();
        $contactsToday = Contact::whereDate('created_at', $today)->count();
        $contactsThisWeek = Contact::where('created_at', '>=', $thisWeek)->count();
        $contactsThisMonth = Contact::where('created_at', '>=', $thisMonth)->count();

        // Visitor Statistics
        $totalVisitors = Visitor::count();
        $uniqueVisitors = Visitor::distinct('visitor_id')->count();
        $visitorsToday = Visitor::whereDate('created_at', $today)->count();
        $uniqueVisitorsToday = Visitor::whereDate('created_at', $today)->distinct('visitor_id')->count();
        $visitorsThisWeek = Visitor::where('created_at', '>=', $thisWeek)->count();
        $visitorsThisMonth = Visitor::where('created_at', '>=', $thisMonth)->count();

        // Today's Contacts for Table
        $todayContacts = Contact::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Activity
        $recentContacts = Contact::orderBy('created_at', 'desc')->limit(5)->get();
        $recentProjects = Project::orderBy('created_at', 'desc')->limit(5)->get();
        $recentBlogs = Blog::orderBy('created_at', 'desc')->limit(5)->get();

        // Visitor Analytics
        $visitorTrend = Visitor::getVisitorTrend(7); // Last 7 days
        $topPages = Visitor::getTopVisitedPages(30, 5);
        $visitorsByCountry = Visitor::getVisitorsByCountry(30);

        return view('admin.dashboard', compact(
            'totalUsers', 'newUsersToday', 'newUsersThisWeek', 'newUsersThisMonth',
            'totalProjects', 'activeProjects', 'featuredProjects', 'projectsThisMonth',
            'totalBlogs', 'publishedBlogs', 'featuredBlogs', 'blogsThisMonth',
            'totalContacts', 'newContacts', 'repliedContacts', 'contactsToday', 'contactsThisWeek', 'contactsThisMonth',
            'totalVisitors', 'uniqueVisitors', 'visitorsToday', 'uniqueVisitorsToday', 'visitorsThisWeek', 'visitorsThisMonth',
            'todayContacts', 'recentContacts', 'recentProjects', 'recentBlogs',
            'visitorTrend', 'topPages', 'visitorsByCountry'
        ));
    }
}
