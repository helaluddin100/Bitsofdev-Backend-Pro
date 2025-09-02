<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Visitor;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if data already exists
        if (Project::count() > 0) {
            $this->command->info('Sample data already exists. Skipping...');
            return;
        }

        // Create sample projects
        $projects = [
            [
                'title' => 'E-commerce Website',
                'slug' => 'ecommerce-website',
                'excerpt' => 'Modern e-commerce platform with advanced features',
                'content' => 'A comprehensive e-commerce solution built with Laravel and Vue.js...',
                'client' => 'TechCorp Inc.',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'in-progress',
                'technologies' => 'Laravel, Vue.js, MySQL, Redis',
                'project_url' => 'https://example-ecommerce.com',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'E-commerce Website Development',
                'meta_description' => 'Professional e-commerce website development services'
            ],
            [
                'title' => 'Mobile Banking App',
                'slug' => 'mobile-banking-app',
                'excerpt' => 'Secure mobile banking application for iOS and Android',
                'content' => 'A secure and user-friendly mobile banking application...',
                'client' => 'BankSecure Ltd.',
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(10),
                'status' => 'completed',
                'technologies' => 'React Native, Node.js, MongoDB',
                'project_url' => 'https://banksecure-app.com',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Mobile Banking App Development',
                'meta_description' => 'Secure mobile banking application development'
            ],
            [
                'title' => 'Portfolio Website',
                'slug' => 'portfolio-website',
                'excerpt' => 'Creative portfolio website for a design agency',
                'content' => 'A stunning portfolio website showcasing creative work...',
                'client' => 'Creative Studio',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 'in-progress',
                'technologies' => 'Next.js, Tailwind CSS, Framer Motion',
                'project_url' => 'https://creative-studio-portfolio.com',
                'priority' => 'medium',
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Portfolio Website Design',
                'meta_description' => 'Creative portfolio website design and development'
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        // Create sample categories
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Mobile Apps', 'slug' => 'mobile-apps'],
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design'],
            ['name' => 'E-commerce', 'slug' => 'ecommerce']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample blogs
        $blogs = [
            [
                'title' => '10 Best Practices for Laravel Development',
                'slug' => '10-best-practices-laravel-development',
                'excerpt' => 'Learn the essential best practices for Laravel development',
                'content' => 'Laravel is a powerful PHP framework that makes web development enjoyable...',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'user_id' => 1,
                'category_id' => 1,
                'views' => 150,
                'is_featured' => true,
                'meta_title' => 'Laravel Development Best Practices',
                'meta_description' => 'Essential best practices for Laravel development'
            ],
            [
                'title' => 'Building Responsive Web Applications',
                'slug' => 'building-responsive-web-applications',
                'excerpt' => 'Complete guide to building responsive web applications',
                'content' => 'Responsive design is crucial for modern web applications...',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'user_id' => 1,
                'category_id' => 1,
                'views' => 89,
                'is_featured' => false,
                'meta_title' => 'Responsive Web Application Development',
                'meta_description' => 'Guide to building responsive web applications'
            ],
            [
                'title' => 'Mobile App Development Trends 2024',
                'slug' => 'mobile-app-development-trends-2024',
                'excerpt' => 'Latest trends in mobile app development for 2024',
                'content' => 'Mobile app development continues to evolve rapidly...',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'user_id' => 1,
                'category_id' => 2,
                'views' => 234,
                'is_featured' => true,
                'meta_title' => 'Mobile App Development Trends 2024',
                'meta_description' => 'Latest mobile app development trends for 2024'
            ]
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }

        // Create sample contacts
        $contacts = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'company' => 'Tech Solutions Inc.',
                'subject' => 'Website Redesign Project',
                'message' => 'We are looking for a complete website redesign for our company. Please let me know about your services and pricing.',
                'project_type' => 'web-development',
                'status' => 'new',
                'created_at' => Carbon::now()->subHours(2)
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@designstudio.com',
                'company' => 'Creative Design Studio',
                'subject' => 'Mobile App Development',
                'message' => 'We need a mobile app for our design portfolio. Can you help us with this project?',
                'project_type' => 'mobile-app',
                'status' => 'read',
                'created_at' => Carbon::now()->subHours(5)
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@startup.com',
                'company' => 'StartupXYZ',
                'subject' => 'E-commerce Platform',
                'message' => 'Looking for an e-commerce platform development. What technologies do you recommend?',
                'project_type' => 'web-development',
                'status' => 'replied',
                'replied_at' => Carbon::now()->subHours(1),
                'created_at' => Carbon::now()->subDays(1)
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@consulting.com',
                'company' => 'Business Consulting Ltd.',
                'subject' => 'UI/UX Design Services',
                'message' => 'We need professional UI/UX design for our new product. Please provide a quote.',
                'project_type' => 'ui-ux-design',
                'status' => 'new',
                'created_at' => Carbon::now()->subMinutes(30)
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@enterprise.com',
                'company' => 'Enterprise Solutions',
                'subject' => 'Consulting Services',
                'message' => 'We need technical consulting for our digital transformation project.',
                'project_type' => 'consulting',
                'status' => 'new',
                'created_at' => Carbon::now()->subMinutes(15)
            ]
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }

        // Create sample visitors
        $visitorData = [];
        for ($i = 0; $i < 50; $i++) {
            $visitorData[] = [
                'visitor_id' => Str::uuid(),
                'ip' => '192.168.1.' . rand(1, 255),
                'location' => json_encode([
                    'country' => ['United States', 'Canada', 'United Kingdom', 'Germany', 'France', 'Australia'][rand(0, 5)],
                    'city' => ['New York', 'Toronto', 'London', 'Berlin', 'Paris', 'Sydney'][rand(0, 5)],
                    'region' => 'Sample Region'
                ]),
                'isp' => 'Sample ISP',
                'device' => ['Desktop', 'Mobile', 'Tablet'][rand(0, 2)],
                'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][rand(0, 3)],
                'os' => ['Windows', 'macOS', 'Linux', 'iOS', 'Android'][rand(0, 4)],
                'page_url' => ['/', '/about', '/contact', '/projects', '/blog'][rand(0, 4)],
                'referrer' => ['google.com', 'facebook.com', 'twitter.com', 'direct'][rand(0, 3)],
                'actions' => json_encode([
                    ['type' => 'page_view', 'timestamp' => Carbon::now()->toISOString()],
                    ['type' => 'click', 'timestamp' => Carbon::now()->toISOString()]
                ]),
                'time_spent' => rand(30, 300),
                'session_id' => Str::random(32),
                'page_entered_at' => Carbon::now()->subMinutes(rand(1, 1440)),
                'page_exited_at' => Carbon::now()->subMinutes(rand(1, 1440))->addSeconds(rand(30, 300)),
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59))
            ];
        }

        foreach ($visitorData as $visitor) {
            Visitor::create($visitor);
        }

        $this->command->info('Dashboard sample data created successfully!');
    }
}
