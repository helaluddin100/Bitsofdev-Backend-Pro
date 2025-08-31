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
        Schema::table('company_processes', function (Blueprint $table) {
            $table->foreignId('about_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_processes', function (Blueprint $table) {
            $table->dropForeign(['about_id']);
            $table->dropColumn('about_id');
        });
    }
};
