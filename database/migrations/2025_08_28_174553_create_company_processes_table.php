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
        Schema::create('company_processes', function (Blueprint $table) {
            $table->id();
            $table->string('step_number');
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('color')->default('from-blue-500 to-blue-600');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_processes');
    }
};
