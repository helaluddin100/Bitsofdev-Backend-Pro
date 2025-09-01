<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'ip',
        'location',
        'isp',
        'device',
        'browser',
        'os',
        'page_url',
        'referrer',
        'actions',
        'time_spent',
        'session_id',
        'page_entered_at',
        'page_exited_at',
    ];

    protected $casts = [
        'location' => 'array',
        'actions' => 'array',
        'page_entered_at' => 'datetime',
        'page_exited_at' => 'datetime',
    ];

    /**
     * Get the total unique visitors count
     */
    public static function getUniqueVisitorsCount($days = null)
    {
        $query = self::distinct('visitor_id');

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        return $query->count('visitor_id');
    }

    /**
     * Get daily unique visitors for the last N days
     */
    public static function getDailyUniqueVisitors($days = 30)
    {
        return self::selectRaw('DATE(created_at) as date, COUNT(DISTINCT visitor_id) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get returning visitors count
     */
    public static function getReturningVisitorsCount($days = 30)
    {
        $visitorsWithMultipleVisits = self::selectRaw('visitor_id, COUNT(*) as visit_count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('visitor_id')
            ->having('visit_count', '>', 1)
            ->count();

        return $visitorsWithMultipleVisits;
    }

    /**
     * Get average session duration
     */
    public static function getAverageSessionDuration($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('time_spent')
            ->avg('time_spent');
    }

    /**
     * Get top visited pages
     */
    public static function getTopVisitedPages($days = 30, $limit = 10)
    {
        return self::selectRaw('page_url, COUNT(*) as visits')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('page_url')
            ->orderBy('visits', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get most clicked actions
     */
    public static function getMostClickedActions($days = 30, $limit = 10)
    {
        $visitors = self::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('actions')
            ->get();

        $actionCounts = [];

        foreach ($visitors as $visitor) {
            $actions = $visitor->actions;
            if (is_array($actions)) {
                foreach ($actions as $action) {
                    if (is_array($action) && isset($action['type'])) {
                        $actionType = $action['type'];
                        $actionCounts[$actionType] = ($actionCounts[$actionType] ?? 0) + 1;
                    }
                }
            }
        }

        // Convert to collection format and sort
        $result = collect();
        foreach ($actionCounts as $actionType => $count) {
            $result->push((object)[
                'action' => $actionType,
                'count' => $count
            ]);
        }

        return $result->sortByDesc('count')->take($limit)->values();
    }

    /**
     * Get visitors by country
     */
    public static function getVisitorsByCountry($days = 30)
    {
        $visitors = self::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('location')
            ->get();

        $countryCounts = [];

        foreach ($visitors as $visitor) {
            $location = $visitor->location;
            if (is_array($location) && isset($location['country'])) {
                $country = $location['country'];
                $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
            }
        }

        // Convert to collection format
        $result = collect();
        foreach ($countryCounts as $country => $count) {
            $result->push((object)[
                'country' => $country,
                'count' => $count
            ]);
        }

        return $result->sortByDesc('count')->values();
    }

    /**
     * Get visitor trend data for charts
     */
    public static function getVisitorTrend($days = 30)
    {
        return self::selectRaw('DATE(created_at) as date, COUNT(DISTINCT visitor_id) as unique_visitors, COUNT(*) as total_visits')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
