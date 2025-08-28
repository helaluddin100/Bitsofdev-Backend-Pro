<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::ordered()->paginate(10);
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/team'), $imageName);
            $data['avatar'] = 'images/team/' . $imageName;
        }

        Team::create($data);

        return redirect()->route('admin.teams.index')->with('success', 'Team member created successfully!');
    }

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('avatar')) {
            // Delete old image
            if ($team->avatar && file_exists(public_path($team->avatar))) {
                unlink(public_path($team->avatar));
            }

            $image = $request->file('avatar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/team'), $imageName);
            $data['avatar'] = 'images/team/' . $imageName;
        }

        $team->update($data);

        return redirect()->route('admin.teams.index')->with('success', 'Team member updated successfully!');
    }

    public function destroy(Team $team)
    {
        if ($team->avatar && file_exists(public_path($team->avatar))) {
            unlink(public_path($team->avatar));
        }

        $team->delete();
        return redirect()->route('admin.teams.index')->with('success', 'Team member deleted successfully!');
    }
}
