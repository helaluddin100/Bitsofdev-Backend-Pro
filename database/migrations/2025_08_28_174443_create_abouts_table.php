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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('hero_title');
            $table->text('hero_description');
            $table->text('story_title');
            $table->text('story_content');
            $table->text('mission_title');
            $table->text('mission_description');
            $table->text('vision_title');
            $table->text('vision_description');
            $table->integer('years_experience')->default(5);
            $table->integer('projects_delivered')->default(100);
            $table->integer('happy_clients')->default(50);
            $table->string('support_availability')->default('24/7');
            $table->text('values_title');
            $table->text('values_description');
            $table->text('process_title');
            $table->text('process_description');
            $table->text('team_title');
            $table->text('team_description');
            $table->text('cta_title');
            $table->text('cta_description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
