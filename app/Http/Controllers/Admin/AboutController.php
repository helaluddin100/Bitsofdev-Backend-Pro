<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\CompanyValue;
use App\Models\CompanyProcess;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::first();
        $values = CompanyValue::ordered()->get();
        $processes = CompanyProcess::ordered()->get();

        return view('admin.about.index', compact('about', 'values', 'processes'));
    }

    public function edit()
    {
        $about = About::first();

        if (!$about) {
            return redirect()->route('admin.about.index')->with('error', 'About information not found. Please create it first.');
        }

        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'hero_title' => 'required|string|max:500',
            'hero_description' => 'required|string',
            'story_title' => 'required|string|max:255',
            'story_content' => 'required|string',
            'mission_title' => 'required|string|max:255',
            'mission_description' => 'required|string',
            'vision_title' => 'required|string|max:255',
            'vision_description' => 'required|string',
            'years_experience' => 'required|integer|min:1',
            'projects_delivered' => 'required|integer|min:1',
            'happy_clients' => 'required|integer|min:1',
            'support_availability' => 'required|string|max:50',
            'values_title' => 'required|string|max:255',
            'values_description' => 'required|string',
            'process_title' => 'required|string|max:255',
            'process_description' => 'required|string',
            'team_title' => 'required|string|max:255',
            'team_description' => 'required|string',
            'cta_title' => 'required|string|max:255',
            'cta_description' => 'required|string',
        ]);

        $about = About::first();

        if (!$about) {
            $about = new About();
        }

        $about->fill($request->all());
        $about->is_active = $request->has('is_active');
        $about->save();

        return redirect()->route('admin.about.index')->with('success', 'About information updated successfully!');
    }

    public function storeValue(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $about = About::first();

        if (!$about) {
            return redirect()->route('admin.about.index')->with('error', 'Please create about information first.');
        }

        CompanyValue::create([
            'about_id' => $about->id,
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.about.index')->with('success', 'Company value added successfully!');
    }

    public function updateValue(Request $request, CompanyValue $value)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $value->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.about.index')->with('success', 'Company value updated successfully!');
    }

    public function destroyValue(CompanyValue $value)
    {
        $value->delete();
        return redirect()->route('admin.about.index')->with('success', 'Company value deleted successfully!');
    }

    public function storeProcess(Request $request)
    {
        $request->validate([
            'step_number' => 'required|string|max:10',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $about = About::first();

        if (!$about) {
            return redirect()->route('admin.about.index')->with('error', 'Please create about information first.');
        }

        CompanyProcess::create([
            'about_id' => $about->id,
            'step_number' => $request->step_number,
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.about.index')->with('success', 'Company process added successfully!');
    }

    public function updateProcess(Request $request, CompanyProcess $process)
    {
        $request->validate([
            'step_number' => 'required|string|max:10',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
        ]);

        $process->update([
            'step_number' => $request->step_number,
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.about.index')->with('success', 'Company process updated successfully!');
    }

    public function destroyProcess(CompanyProcess $process)
    {
        $process->delete();
        return redirect()->route('admin.about.index')->with('success', 'Company process deleted successfully!');
    }
}
