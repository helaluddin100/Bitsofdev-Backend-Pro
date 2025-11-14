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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->json('phones')->nullable();
            $table->string('company')->nullable();
            $table->string('category')->nullable();
            $table->text('address')->nullable();
            $table->string('full_address')->nullable();
            $table->string('street')->nullable();
            $table->string('municipality')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('bing_maps_url')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->text('rating_info')->nullable();
            $table->integer('review_count')->default(0);
            $table->text('review_url')->nullable();
            $table->text('open_hours')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('website')->nullable();
            $table->string('domain')->nullable();
            $table->json('social_medias')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('claimed')->default(false);
            $table->timestamp('last_contacted_at')->nullable();
            $table->integer('contact_count')->default(0);
            $table->string('cid')->nullable();
            $table->string('place_id')->nullable();
            $table->string('kgmid')->nullable();
            $table->string('plus_code')->nullable();
            $table->text('google_knowledge_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('phone');
            $table->index('category');
            $table->index('is_active');
            $table->index('last_contacted_at');
            $table->index(['category', 'is_active']);
            $table->index('municipality');
            $table->index('claimed');
            $table->index(['email', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

