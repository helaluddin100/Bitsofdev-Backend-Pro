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
        // Skip if leads table doesn't exist
        if (!Schema::hasTable('leads')) {
            return;
        }

        // All columns are already in the initial leads table migration
        // This migration is kept for backward compatibility
        // Only add indexes if they don't exist
        
        // Try to add indexes (will fail silently if they already exist)
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->index(['category', 'is_active'], 'leads_category_is_active_index');
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('municipality', 'leads_municipality_index');
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->index('claimed', 'leads_claimed_index');
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
        
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->index(['email', 'phone'], 'leads_email_phone_index');
            });
        } catch (\Exception $e) {
            // Index already exists, ignore
        }
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
