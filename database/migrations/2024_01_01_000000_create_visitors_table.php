<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id')->index(); // Unique visitor identifier
            $table->string('ip')->nullable();
            $table->json('location')->nullable(); // Country, city, etc.
            $table->string('isp')->nullable();
            $table->string('device')->nullable(); // Mobile/Desktop/Tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->text('page_url');
            $table->text('referrer')->nullable();
            $table->json('actions')->nullable(); // Clicked buttons/links
            $table->integer('time_spent')->default(0); // Time spent in seconds
            $table->string('session_id')->nullable(); // To group page visits
            $table->timestamp('page_entered_at');
            $table->timestamp('page_exited_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['visitor_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });

        // Add index on page_url with length limit for MySQL compatibility
        DB::statement('ALTER TABLE visitors ADD INDEX visitors_page_url_created_at_index (page_url(255), created_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
