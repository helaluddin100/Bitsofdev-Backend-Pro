<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        $subscribers = $query->paginate(15);
        $stats = $this->getStatistics();

        return view('admin.newsletter-subscribers.index', compact('subscribers', 'stats'));
    }

    private function getStatistics()
    {
        return [
            'total' => NewsletterSubscriber::count(),
            'subscribed' => NewsletterSubscriber::subscribed()->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'today' => NewsletterSubscriber::whereDate('created_at', today())->count(),
            'this_month' => NewsletterSubscriber::whereMonth('created_at', now()->month)->count(),
        ];
    }
}
