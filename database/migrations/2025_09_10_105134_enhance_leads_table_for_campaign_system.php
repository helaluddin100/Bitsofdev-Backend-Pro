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
        Schema::table('leads', function (Blueprint $table) {
            // Add missing fields for comprehensive lead management
            $table->string('full_address')->nullable()->after('address');
            $table->string('street')->nullable()->after('full_address');
            $table->string('municipality')->nullable()->after('street');
            $table->json('phones')->nullable()->after('phone'); // Multiple phone numbers
            $table->boolean('claimed')->default(false)->after('is_active');
            $table->integer('review_count')->default(0)->after('claimed');
            $table->decimal('average_rating', 3, 2)->nullable()->after('review_count');
            $table->text('review_url')->nullable()->after('average_rating');
            $table->text('google_maps_url')->nullable()->after('review_url');
            $table->string('domain')->nullable()->after('website');
            $table->text('opening_hours')->nullable()->after('open_hours');

            // Google Business Information
            $table->string('cid')->nullable()->after('opening_hours');
            $table->string('place_id')->nullable()->after('cid');
            $table->string('kgmid')->nullable()->after('place_id');
            $table->string('plus_code')->nullable()->after('kgmid');
            $table->text('google_knowledge_url')->nullable()->after('plus_code');

            // Additional tracking fields
            $table->integer('contact_count')->default(0)->after('last_contacted_at');

            // Add indexes for better performance
            $table->index(['category', 'is_active']);
            $table->index(['municipality']);
            $table->index(['claimed']);
            $table->index(['email', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn([
                'full_address',
                'street',
                'municipality',
                'phones',
                'claimed',
                'review_count',
                'average_rating',
                'review_url',
                'google_maps_url',
                'domain',
                'opening_hours',
                'cid',
                'place_id',
                'kgmid',
                'plus_code',
                'google_knowledge_url',
                'contact_count'
            ]);

            // Drop the added indexes
            $table->dropIndex(['category', 'is_active']);
            $table->dropIndex(['municipality']);
            $table->dropIndex(['claimed']);
            $table->dropIndex(['email', 'phone']);
        });
    }
};
