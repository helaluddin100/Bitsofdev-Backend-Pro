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
                'excerpt' => 'A full-featured e-commerce platform built with Laravel and Vue.js',
                'content' => 'This project is a comprehensive e-commerce solution that includes user management, product catalog, shopping cart, payment processing, and order management. Built with modern technologies and best practices.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'TechCorp Inc.',
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'completed',
                'technologies' => 'Laravel, Vue.js, MySQL, Redis, Stripe',
                'project_url' => 'https://example-ecommerce.com',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'E-Commerce Platform - Modern Online Store Solution',
                'meta_description' => 'Full-featured e-commerce platform with advanced features and modern technology stack.'
            ],
            [
                'title' => 'Mobile Banking App',
                'excerpt' => 'Cross-platform mobile banking application for iOS and Android',
                'content' => 'A secure and user-friendly mobile banking application that allows users to manage their accounts, transfer money, pay bills, and view transaction history. Built with React Native for cross-platform compatibility.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'BankSecure',
                'start_date' => '2024-03-01',
                'end_date' => '2024-12-31',
                'status' => 'in-progress',
                'technologies' => 'React Native, Node.js, MongoDB, Firebase',
                'project_url' => null,
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Mobile Banking App - Secure Financial Management',
                'meta_description' => 'Cross-platform mobile banking application with advanced security features.'
            ],
            [
                'title' => 'Project Management Dashboard',
                'excerpt' => 'Web-based project management tool with real-time collaboration',
                'content' => 'A comprehensive project management solution that helps teams organize tasks, track progress, and collaborate effectively. Features include task management, time tracking, team collaboration, and reporting.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'StartupXYZ',
                'start_date' => '2024-02-01',
                'end_date' => '2024-08-31',
                'status' => 'planning',
                'technologies' => 'Next.js, TypeScript, PostgreSQL, Socket.io',
                'project_url' => null,
                'priority' => 'medium',
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Project Management Dashboard - Team Collaboration Tool',
                'meta_description' => 'Web-based project management solution with real-time collaboration features.'
            ],
            [
                'title' => 'AI-Powered Chatbot',
                'excerpt' => 'Intelligent chatbot with natural language processing capabilities',
                'content' => 'An advanced AI chatbot that can understand and respond to user queries in natural language. Features include sentiment analysis, multi-language support, and integration with various platforms.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'TechInnovate',
                'start_date' => '2024-04-01',
                'end_date' => '2024-09-30',
                'status' => 'completed',
                'technologies' => 'Python, TensorFlow, Flask, NLP, OpenAI',
                'project_url' => 'https://ai-chatbot-demo.com',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'AI-Powered Chatbot - Natural Language Processing',
                'meta_description' => 'Intelligent chatbot solution with advanced AI and NLP capabilities.'
            ],
            [
                'title' => 'Social Media Platform',
                'excerpt' => 'Modern social media platform with real-time messaging and content sharing',
                'content' => 'A feature-rich social media platform that includes user profiles, content sharing, real-time messaging, and community features. Built with modern web technologies for optimal performance.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'SocialConnect',
                'start_date' => '2024-01-15',
                'end_date' => '2024-07-15',
                'status' => 'completed',
                'technologies' => 'Vue.js, Express, Socket.io, Redis, MongoDB',
                'project_url' => 'https://social-platform-demo.com',
                'priority' => 'medium',
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Social Media Platform - Modern Social Networking',
                'meta_description' => 'Feature-rich social media platform with real-time messaging and content sharing.'
            ],
            [
                'title' => 'IoT Dashboard',
                'excerpt' => 'IoT device management dashboard with real-time monitoring and control',
                'content' => 'A comprehensive IoT dashboard that allows users to monitor and control connected devices in real-time. Features include device management, data visualization, alerts, and automation.',
                'featured_image' => 'images/projects/1756371321_xVFuYsgWfh.webp',
                'client' => 'IoT Solutions',
                'start_date' => '2024-05-01',
                'end_date' => '2024-11-30',
                'status' => 'in-progress',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'IoT Dashboard - Device Management & Monitoring',
                'meta_description' => 'Real-time IoT device management dashboard with advanced monitoring capabilities.'
            ]
        ];

        foreach ($projects as $project) {
            $project['slug'] = \Illuminate\Support\Str::slug($project['title']);
            Project::create($project);
        }
    }
}
