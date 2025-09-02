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
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_session_id')->constrained()->onDelete('cascade');
            $table->enum('sender', ['visitor', 'ai']);
            $table->text('message');
            $table->string('message_type')->default('text'); // text, image, file, etc.
            $table->json('metadata')->nullable(); // Additional data like timestamps, context, etc.
            $table->timestamps();

            $table->index(['conversation_session_id', 'created_at']);
            $table->index('sender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
    }
};
