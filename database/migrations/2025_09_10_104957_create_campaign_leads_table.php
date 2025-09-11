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
        Schema::create('campaign_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');

            // Status tracking
            $table->enum('status', [
                'fresh',        // Added to campaign but not sent
                'sent',         // Initial email sent
                'reminder_1',   // First reminder sent
                'reminder_2',   // Second reminder sent
                'responded',    // Lead responded
                'bounced',      // Email bounced
                'unsubscribed'  // Lead unsubscribed
            ])->default('fresh');

            // Timestamps for tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('reminder_1_sent_at')->nullable();
            $table->timestamp('reminder_2_sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();

            // Email tracking
            $table->string('email_address')->nullable(); // Snapshot of email at time of send
            $table->text('email_subject')->nullable();
            $table->longText('email_body')->nullable(); // Snapshot of email content

            // Response tracking
            $table->text('response_message')->nullable();
            $table->text('response_source')->nullable(); // email, phone, etc.

            // Additional data
            $table->json('metadata')->nullable(); // Store additional tracking data
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index(['campaign_id', 'status']);
            $table->index(['lead_id', 'status']);
            $table->index(['status', 'sent_at']);
            $table->index(['campaign_id', 'lead_id']); // Unique constraint
            $table->index(['responded_at']);

            // Ensure a lead can only be in a campaign once
            $table->unique(['campaign_id', 'lead_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_leads');
    }
};
