<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Visitor;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TodayDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create today's contacts
        $todayContacts = [
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

        foreach ($todayContacts as $contact) {
            Contact::create($contact);
        }

        // Create today's visitors
        $visitorData = [];
        for ($i = 0; $i < 25; $i++) {
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
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59))
            ];
        }

        foreach ($visitorData as $visitor) {
            Visitor::create($visitor);
        }

        $this->command->info('Today\'s sample data created successfully!');
    }
}
