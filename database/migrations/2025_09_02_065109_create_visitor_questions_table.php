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
        Schema::create('visitor_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('visitor_session')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status')->default('pending'); // pending, answered, converted
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_converted')->default(false);
            $table->integer('qa_pair_id')->nullable(); // If matched with existing Q&A
            $table->text('admin_notes')->nullable();
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
        Schema::dropIfExists('visitor_questions');
    }
};
