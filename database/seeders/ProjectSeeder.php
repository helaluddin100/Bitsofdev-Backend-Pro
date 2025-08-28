<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'E-Commerce Platform',
                'description' => 'A full-featured e-commerce platform built with Laravel and Vue.js',
                'content' => 'This project is a comprehensive e-commerce solution that includes user management, product catalog, shopping cart, payment processing, and order management. Built with modern technologies and best practices.',
                'client' => 'TechCorp Inc.',
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'completed',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Stripe'],
                'project_url' => 'https://example-ecommerce.com',
                'github_url' => 'https://github.com/example/ecommerce',
                'priority' => 1,
                'is_featured' => true
            ],
            [
                'title' => 'Mobile Banking App',
                'description' => 'Cross-platform mobile banking application for iOS and Android',
                'content' => 'A secure and user-friendly mobile banking application that allows users to manage their accounts, transfer money, pay bills, and view transaction history. Built with React Native for cross-platform compatibility.',
                'client' => 'BankSecure',
                'start_date' => '2024-03-01',
                'end_date' => '2024-12-31',
                'status' => 'in_progress',
                'technologies' => ['React Native', 'Node.js', 'MongoDB', 'Firebase'],
                'project_url' => null,
                'github_url' => 'https://github.com/example/banking-app',
                'priority' => 2,
                'is_featured' => true
            ],
            [
                'title' => 'Project Management Dashboard',
                'description' => 'Web-based project management tool with real-time collaboration',
                'content' => 'A comprehensive project management solution that helps teams organize tasks, track progress, and collaborate effectively. Features include task management, time tracking, team collaboration, and reporting.',
                'client' => 'StartupXYZ',
                'start_date' => '2024-02-01',
                'end_date' => '2024-08-31',
                'status' => 'planning',
                'technologies' => ['Next.js', 'TypeScript', 'PostgreSQL', 'Socket.io'],
                'project_url' => null,
                'github_url' => null,
                'priority' => 3,
                'is_featured' => false
            ]
        ];

        foreach ($projects as $project) {
            $project['slug'] = \Illuminate\Support\Str::slug($project['title']);
            Project::create($project);
        }
    }
}
