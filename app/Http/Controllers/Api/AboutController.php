<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\CompanyValue;
use App\Models\CompanyProcess;
use App\Models\Team;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::active()->first();

        if (!$about) {
            return response()->json([
                'success' => false,
                'message' => 'About information not found'
            ], 404);
        }

        $data = [
            'company_info' => [
                'name' => $about->company_name,
                'hero_title' => $about->hero_title,
                'hero_description' => $about->hero_description,
                'story_title' => $about->story_title,
                'story_content' => $about->story_content,
                'mission_title' => $about->mission_title,
                'mission_description' => $about->mission_description,
                'vision_title' => $about->vision_title,
                'vision_description' => $about->vision_description,
            ],
            'stats' => [
                'years_experience' => $about->years_experience,
                'projects_delivered' => $about->projects_delivered,
                'happy_clients' => $about->happy_clients,
                'support_availability' => $about->support_availability,
            ],
            'sections' => [
                'values' => [
                    'title' => $about->values_title,
                    'description' => $about->values_description,
                ],
                'process' => [
                    'title' => $about->process_title,
                    'description' => $about->process_description,
                ],
                'team' => [
                    'title' => $about->team_title,
                    'description' => $about->team_description,
                ],
                'cta' => [
                    'title' => $about->cta_title,
                    'description' => $about->cta_description,
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function values()
    {
        $values = CompanyValue::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $values
        ]);
    }

    public function processes()
    {
        $processes = CompanyProcess::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $processes
        ]);
    }

    public function team()
    {
        $team = Team::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }

    public function all()
    {
        $about = About::active()->first();

        if (!$about) {
            return response()->json([
                'success' => false,
                'message' => 'About information not found'
            ], 404);
        }

        $values = CompanyValue::active()->ordered()->get();
        $processes = CompanyProcess::active()->ordered()->get();
        $team = Team::active()->ordered()->get();

        $data = [
            'company_info' => [
                'name' => $about->company_name,
                'hero_title' => $about->hero_title,
                'hero_description' => $about->hero_description,
                'story_title' => $about->story_title,
                'story_content' => $about->story_content,
                'mission_title' => $about->mission_title,
                'mission_description' => $about->mission_description,
                'vision_title' => $about->vision_title,
                'vision_description' => $about->vision_description,
            ],
            'stats' => [
                'years_experience' => $about->years_experience,
                'projects_delivered' => $about->projects_delivered,
                'happy_clients' => $about->happy_clients,
                'support_availability' => $about->support_availability,
            ],
            'sections' => [
                'values' => [
                    'title' => $about->values_title,
                    'description' => $about->values_description,
                    'items' => $values
                ],
                'process' => [
                    'title' => $about->process_title,
                    'description' => $about->process_description,
                    'items' => $processes
                ],
                'team' => [
                    'title' => $about->team_title,
                    'description' => $about->team_description,
                    'items' => $team
                ],
                'cta' => [
                    'title' => $about->cta_title,
                    'description' => $about->cta_description,
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
