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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->enum('schedule_type', ['immediate', 'scheduled'])->default('immediate');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_leads')->default(0);
            $table->integer('emails_sent')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('reminder_1_sent')->default(0);
            $table->integer('reminder_2_sent')->default(0);
            $table->integer('emails_failed')->default(0);
            $table->integer('responses_received')->default(0);
            $table->integer('response_count')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('reminders_enabled')->default(false);
            $table->boolean('enable_reminders')->default(false);
            $table->integer('reminder_days_1')->nullable();
            $table->integer('reminder_1_days')->nullable();
            $table->integer('reminder_days_2')->nullable();
            $table->integer('reminder_2_days')->nullable();
            $table->string('reminder_subject_1')->nullable();
            $table->string('reminder_1_subject')->nullable();
            $table->text('reminder_body_1')->nullable();
            $table->text('reminder_1_body')->nullable();
            $table->string('reminder_subject_2')->nullable();
            $table->string('reminder_2_subject')->nullable();
            $table->text('reminder_body_2')->nullable();
            $table->text('reminder_2_body')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('is_active');
            $table->index('category');
            $table->index('schedule_type');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};

