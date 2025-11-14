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
        Schema::table('campaign_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_leads', 'reminder_3_sent_at')) {
                $table->timestamp('reminder_3_sent_at')->nullable()->after('reminder_2_sent_at');
            }
        });

        // Update enum using raw SQL (MySQL specific)
        DB::statement("ALTER TABLE campaign_leads MODIFY COLUMN status ENUM('fresh', 'sent', 'reminder_1', 'reminder_2', 'reminder_3', 'responded', 'bounced', 'unsubscribed') DEFAULT 'fresh'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_leads', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_leads', 'reminder_3_sent_at')) {
                $table->dropColumn('reminder_3_sent_at');
            }
        });

        // Revert enum
        DB::statement("ALTER TABLE campaign_leads MODIFY COLUMN status ENUM('fresh', 'sent', 'reminder_1', 'reminder_2', 'responded', 'bounced', 'unsubscribed') DEFAULT 'fresh'");
    }
};
