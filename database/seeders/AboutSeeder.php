<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\About;
use App\Models\CompanyValue;
use App\Models\CompanyProcess;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main about information
        $about = About::create([
            'company_name' => 'sparkedev',
            'hero_title' => 'About sparkedev',
            'hero_description' => 'We\'re a passionate team of developers, designers, and digital strategists dedicated to creating exceptional web experiences that drive business growth.',
            'story_title' => 'Our Story',
            'story_content' => 'Founded in 2019, sparkedev started as a small team with a big vision: to help businesses leverage technology to achieve their goals. What began as a passion project has grown into a full-service digital agency. We\'ve had the privilege of working with startups, established businesses, and everything in between. Each project teaches us something new and helps us refine our craft and expand our expertise. Today, we\'re proud to be trusted partners for businesses looking to make their mark in the digital world. Our commitment to excellence, innovation, and client success drives everything we do.',
            'mission_title' => 'Our Mission',
            'mission_description' => 'To deliver exceptional digital solutions that drive real business results.',
            'vision_title' => 'Our Vision',
            'vision_description' => 'To be the leading digital agency that transforms businesses through innovative technology solutions.',
            'years_experience' => 5,
            'projects_delivered' => 100,
            'happy_clients' => 50,
            'support_availability' => '24/7',
            'values_title' => 'Our Values',
            'values_description' => 'The principles that guide everything we do and shape our culture',
            'process_title' => 'How We Work',
            'process_description' => 'Our proven process for delivering exceptional results and exceeding expectations',
            'team_title' => 'Meet Our Team',
            'team_description' => 'The talented individuals behind every successful project',
            'cta_title' => 'Ready to Work Together?',
            'cta_description' => 'Let\'s discuss how we can help bring your vision to life and create something amazing together',
            'is_active' => true
        ]);

        // Create company values
        $values = [
            [
                'title' => 'Mission Driven',
                'description' => 'We\'re committed to delivering exceptional digital solutions that drive real business results.',
                'icon' => 'Target',
                'color' => 'from-blue-500 to-blue-600',
                'sort_order' => 1
            ],
            [
                'title' => 'Client Focused',
                'description' => 'Your success is our success. We work closely with you every step of the way.',
                'icon' => 'Users',
                'color' => 'from-purple-500 to-purple-600',
                'sort_order' => 2
            ],
            [
                'title' => 'Excellence First',
                'description' => 'We maintain the highest standards in code quality, design, and project delivery.',
                'icon' => 'Award',
                'color' => 'from-green-500 to-green-600',
                'sort_order' => 3
            ],
            [
                'title' => 'Passion Powered',
                'description' => 'We love what we do, and it shows in every project we deliver.',
                'icon' => 'Heart',
                'color' => 'from-red-500 to-red-600',
                'sort_order' => 4
            ]
        ];

        foreach ($values as $value) {
            CompanyValue::create([
                'about_id' => $about->id,
                'title' => $value['title'],
                'description' => $value['description'],
                'icon' => $value['icon'],
                'color' => $value['color'],
                'sort_order' => $value['sort_order'],
                'is_active' => true
            ]);
        }

        // Create company processes
        $processes = [
            [
                'step_number' => '01',
                'title' => 'Discovery',
                'description' => 'We start by understanding your business, goals, and requirements through detailed consultation.',
                'icon' => 'Lightbulb',
                'color' => 'from-blue-500 to-blue-600',
                'sort_order' => 1
            ],
            [
                'step_number' => '02',
                'title' => 'Planning',
                'description' => 'We create a comprehensive project plan, timeline, and technical architecture.',
                'icon' => 'Target',
                'color' => 'from-purple-500 to-purple-600',
                'sort_order' => 2
            ],
            [
                'step_number' => '03',
                'title' => 'Development',
                'description' => 'Our team brings your vision to life using cutting-edge technologies and best practices.',
                'icon' => 'Zap',
                'color' => 'from-green-500 to-green-600',
                'sort_order' => 3
            ],
            [
                'step_number' => '04',
                'title' => 'Launch & Support',
                'description' => 'We ensure a smooth launch and provide ongoing support to help you succeed.',
                'icon' => 'Rocket',
                'color' => 'from-red-500 to-red-600',
                'sort_order' => 4
            ]
        ];

        foreach ($processes as $process) {
            CompanyProcess::create([
                'about_id' => $about->id,
                'step_number' => $process['step_number'],
                'title' => $process['title'],
                'description' => $process['description'],
                'icon' => $process['icon'],
                'color' => $process['color'],
                'sort_order' => $process['sort_order'],
                'is_active' => true
            ]);
        }
    }
}
