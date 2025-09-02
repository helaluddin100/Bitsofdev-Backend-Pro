<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_i_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ai_provider')->default('gemini'); // gemini, own_ai, none
            $table->boolean('training_mode')->default(false); // true = use own AI, false = use external AI
            $table->integer('learning_threshold')->default(10); // minimum responses to activate own AI
            $table->boolean('use_static_responses')->default(false); // enable/disable static responses
            $table->json('ai_config')->nullable(); // additional AI configuration
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('a_i_settings');
    }
};
