<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $team = Team::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }

    public function featured()
    {
        $featuredMembers = Team::active()
            ->featured()
            ->ordered()
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $featuredMembers
        ]);
    }

    public function show($id)
    {
        $member = Team::active()
            ->find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }
}
