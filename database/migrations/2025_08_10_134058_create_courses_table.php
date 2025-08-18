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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('title')->unique();
            $table->text('description')->nullable(true);
            $table->uuid('teacher_id')->index();
            $table->enum('type', ['public', 'private'])->default('public');
            $table->string('access_code')->nullable(true);
            $table->string('cover_path')->nullable(true);
            $table->timestamps();

            $table->foreign('teacher_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
