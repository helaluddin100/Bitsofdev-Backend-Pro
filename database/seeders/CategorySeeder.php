<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'description' => 'Articles about web development technologies and practices',
                'color' => '#3B82F6'
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Mobile app development tutorials and guides',
                'color' => '#10B981'
            ],
            [
                'name' => 'Design',
                'description' => 'UI/UX design principles and best practices',
                'color' => '#F59E0B'
            ],
            [
                'name' => 'Technology',
                'description' => 'General technology news and insights',
                'color' => '#8B5CF6'
            ],
            [
                'name' => 'Tutorials',
                'description' => 'Step-by-step guides and tutorials',
                'color' => '#EF4444'
            ]
        ];

        foreach ($categories as $category) {
            $category['slug'] = \Illuminate\Support\Str::slug($category['name']);
            Category::create($category);
        }
    }
}
