<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->uuid('answer_id')->primary();
            $table->uuid('attempt_id')->index();
            $table->uuid('question_id')->index();
            $table->uuid('option_id')->nullable();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();

            $table->foreign('attempt_id')->references('attempt_id')->on('quiz_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('quiz_questions')->onDelete('cascade');
            $table->foreign('option_id')->references('option_id')->on('quiz_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_answers');
    }
};
