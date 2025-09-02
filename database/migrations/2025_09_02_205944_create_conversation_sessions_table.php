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
        Schema::create('conversation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('visitor_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->integer('message_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['session_id', 'is_active']);
            $table->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_sessions');
    }
};
