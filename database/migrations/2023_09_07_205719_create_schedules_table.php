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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('status');
            $table->string('name');
            $table->dateTime('begin_time');
            $table->dateTime('end_time')->nullable();
            $table->string('repetition_state', 7)->nullable();
            $table->integer('elapsed_days')->nullable();
            $table->string('memo')->nullable();
            $table->unsignedBigInteger('tag1_id')->nullable();
            $table->unsignedBigInteger('tag2_id')->nullable();
            $table->unsignedBigInteger('tag3_id')->nullable();
            $table->boolean('is_set_alerm')->default(false);
            $table->dateTime('alerm_time')->nullable();
            $table->boolean('is_duplication')->default(false);
            $table->string('color');
            $table->string('template_name')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tag1_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('tag2_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('tag3_id')->references('id')->on('tags')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
