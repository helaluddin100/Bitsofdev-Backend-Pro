<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = [
            [
                'name' => 'John Doe',
                'position' => 'Lead Developer',
                'bio' => 'Experienced full-stack developer with 8+ years of experience in web and mobile development. Specializes in Laravel, React, and Node.js.',
                'email' => 'john@example.com',
                'linkedin_url' => 'https://linkedin.com/in/johndoe',
                'github_url' => 'https://github.com/johndoe',
                'order' => 1,
                'is_featured' => true
            ],
            [
                'name' => 'Jane Smith',
                'position' => 'UI/UX Designer',
                'bio' => 'Creative designer passionate about creating beautiful and functional user experiences. Expert in Figma, Adobe Creative Suite, and design systems.',
                'email' => 'jane@example.com',
                'linkedin_url' => 'https://linkedin.com/in/janesmith',
                'website_url' => 'https://janesmith.design',
                'order' => 2,
                'is_featured' => true
            ],
            [
                'name' => 'Mike Johnson',
                'position' => 'Mobile Developer',
                'bio' => 'Mobile app specialist with expertise in React Native, Flutter, and native iOS/Android development. Passionate about creating smooth mobile experiences.',
                'email' => 'mike@example.com',
                'linkedin_url' => 'https://linkedin.com/in/mikejohnson',
                'github_url' => 'https://github.com/mikejohnson',
                'order' => 3,
                'is_featured' => false
            ],
            [
                'name' => 'Sarah Wilson',
                'position' => 'Backend Developer',
                'bio' => 'Backend specialist with deep knowledge of databases, APIs, and server architecture. Expert in Laravel, Node.js, and cloud services.',
                'email' => 'sarah@example.com',
                'linkedin_url' => 'https://linkedin.com/in/sarahwilson',
                'github_url' => 'https://github.com/sarahwilson',
                'order' => 4,
                'is_featured' => false
            ]
        ];

        foreach ($team as $member) {
            Team::create($member);
        }
    }
}
