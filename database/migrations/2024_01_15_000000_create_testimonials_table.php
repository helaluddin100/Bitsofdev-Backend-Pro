<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->string('project_type')->nullable();
            $table->string('project_name')->nullable();
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // For additional data like social links, etc.
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['is_active', 'is_featured']);
            $table->index(['rating', 'is_active']);
            $table->index(['project_type', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
