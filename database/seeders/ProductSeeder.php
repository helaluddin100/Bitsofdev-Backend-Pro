<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'Web Analytics Dashboard',
                'excerpt' => 'Real-time analytics and reporting dashboard for websites and apps',
                'content' => 'A comprehensive analytics dashboard that tracks user behavior, traffic sources, conversions, and key metrics. Built with modern charts and real-time updates for data-driven decisions.',
                'featured_image' => null,
                'client' => 'DataFlow Inc.',
                'start_date' => '2024-06-01',
                'end_date' => '2024-10-15',
                'status' => 'completed',
                'technologies' => 'React, Next.js, Chart.js, PostgreSQL, Redis',
                'product_url' => 'https://analytics-demo.example.com',
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'Web Analytics Dashboard - Real-time Reporting',
                'meta_description' => 'Real-time analytics and reporting dashboard for websites and applications.',
            ],
            [
                'title' => 'CRM Software Suite',
                'excerpt' => 'Customer relationship management platform for sales and support teams',
                'content' => 'Full-featured CRM that helps teams manage contacts, deals, tasks, and communications. Includes email integration, pipeline management, and detailed reporting for sales and support.',
                'featured_image' => null,
                'client' => 'SalesPro',
                'start_date' => '2024-04-01',
                'end_date' => '2024-12-31',
                'status' => 'in-progress',
                'technologies' => 'Laravel, Vue.js, MySQL, Elasticsearch',
                'product_url' => null,
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'CRM Software - Sales & Support Management',
                'meta_description' => 'Customer relationship management platform for sales and support teams.',
            ],
            [
                'title' => 'Inventory Management System',
                'excerpt' => 'Stock and warehouse management with barcode and multi-location support',
                'content' => 'Inventory system with purchase orders, stock levels, low-stock alerts, barcode scanning, and multi-warehouse support. Integrates with accounting and e-commerce platforms.',
                'featured_image' => null,
                'client' => 'RetailMax',
                'start_date' => '2024-03-15',
                'end_date' => '2024-09-30',
                'status' => 'completed',
                'technologies' => 'Node.js, React, MongoDB, REST API',
                'product_url' => 'https://inventory-demo.example.com',
                'priority' => 'medium',
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'Inventory Management - Stock & Warehouse',
                'meta_description' => 'Stock and warehouse management with barcode and multi-location support.',
            ],
            [
                'title' => 'Learning Management System',
                'excerpt' => 'Online course platform with quizzes, certificates, and progress tracking',
                'content' => 'LMS for creating and selling courses. Features include video lessons, quizzes, certificates, progress tracking, discussion forums, and payment integration for instructors.',
                'featured_image' => null,
                'client' => 'EduTech',
                'start_date' => '2024-02-01',
                'end_date' => '2024-08-31',
                'status' => 'review',
                'technologies' => 'Laravel, Tailwind CSS, MySQL, Stripe, Vimeo',
                'product_url' => null,
                'priority' => 'high',
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => 'LMS - Online Course Platform',
                'meta_description' => 'Learning management system with quizzes, certificates, and progress tracking.',
            ],
            [
                'title' => 'HR & Payroll Portal',
                'excerpt' => 'Employee management, attendance, leave, and payroll in one platform',
                'content' => 'HR portal for employee profiles, attendance, leave requests, performance reviews, and payroll processing. Includes role-based access and compliance reporting.',
                'featured_image' => null,
                'client' => 'HR Solutions Ltd.',
                'start_date' => '2024-05-01',
                'end_date' => '2024-11-30',
                'status' => 'planning',
                'technologies' => 'PHP, Laravel, Vue.js, MySQL, PDF export',
                'product_url' => null,
                'priority' => 'medium',
                'is_featured' => false,
                'is_active' => true,
                'meta_title' => 'HR & Payroll Portal - Employee Management',
                'meta_description' => 'Employee management, attendance, leave, and payroll in one platform.',
            ],
        ];

        foreach ($products as $item) {
            $item['slug'] = Str::slug($item['title']);
            Product::create($item);
        }
    }
}
