<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\Category;
use App\Models\User;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $blogs = [
            [
                'title' => 'Getting Started with Laravel 9',
                'excerpt' => 'Learn the basics of Laravel 9 and build your first web application with this powerful PHP framework.',
                'content' => 'Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as authentication, routing, sessions, and caching.',
                'category_id' => $categories->where('name', 'Web Development')->first()->id,
                'status' => 'published',
                'is_featured' => true
            ],
            [
                'title' => 'Modern JavaScript ES6+ Features',
                'excerpt' => 'Explore the latest JavaScript features that make your code more readable and maintainable.',
                'content' => 'ES6 introduced many new features to JavaScript that make the language more powerful and easier to work with. From arrow functions to destructuring, these features have become essential for modern web development.',
                'category_id' => $categories->where('name', 'Web Development')->first()->id,
                'status' => 'published',
                'is_featured' => false
            ],
            [
                'title' => 'UI/UX Design Principles for Developers',
                'excerpt' => 'Understanding design principles can help developers create better user experiences.',
                'content' => 'Good design is not just about aesthetics. It\'s about creating intuitive, accessible, and enjoyable user experiences. As developers, understanding basic design principles can significantly improve the quality of our applications.',
                'category_id' => $categories->where('name', 'Design')->first()->id,
                'status' => 'published',
                'is_featured' => true
            ]
        ];

        foreach ($blogs as $blog) {
            $blog['slug'] = \Illuminate\Support\Str::slug($blog['title']);
            Blog::create(array_merge($blog, [
                'user_id' => $user->id,
                'published_at' => now()
            ]));
        }
    }
}
