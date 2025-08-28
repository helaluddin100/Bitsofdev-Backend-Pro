<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);

        // Seed our new features
        $this->call(CategorySeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(PricingSeeder::class);
    }
}
