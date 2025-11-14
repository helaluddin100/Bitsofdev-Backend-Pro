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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'reminder_3_days')) {
                $table->integer('reminder_3_days')->nullable()->after('reminder_2_body');
            }
            if (!Schema::hasColumn('campaigns', 'reminder_3_subject')) {
                $table->string('reminder_3_subject')->nullable()->after('reminder_3_days');
            }
            if (!Schema::hasColumn('campaigns', 'reminder_3_body')) {
                $table->text('reminder_3_body')->nullable()->after('reminder_3_subject');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'reminder_3_body')) {
                $table->dropColumn('reminder_3_body');
            }
            if (Schema::hasColumn('campaigns', 'reminder_3_subject')) {
                $table->dropColumn('reminder_3_subject');
            }
            if (Schema::hasColumn('campaigns', 'reminder_3_days')) {
                $table->dropColumn('reminder_3_days');
            }
        });
    }
};
