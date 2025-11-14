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
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_lead_id')->nullable()->constrained('campaign_leads')->onDelete('cascade');
            $table->timestamp('response_date');
            $table->text('response_message')->nullable();
            $table->enum('response_type', ['email', 'phone', 'website', 'social', 'in_person', 'other'])->nullable();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->boolean('is_qualified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('lead_id');
            $table->index('campaign_id');
            $table->index('campaign_lead_id');
            $table->index('response_date');
            $table->index('is_qualified');
            $table->index('response_type');
            $table->index('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};

