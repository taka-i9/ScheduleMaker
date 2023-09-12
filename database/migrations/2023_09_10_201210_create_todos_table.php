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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('status');
            $table->string('type');
            $table->string('name');
            $table->dateTime('deadline');
            $table->integer('required_minutes');
            $table->integer('rest_minutes');
            $table->integer('repetition_state')->nullable();
            $table->string('memo')->nullable();
            $table->unsignedBigInteger('tag1_id')->nullable();
            $table->unsignedBigInteger('tag2_id')->nullable();
            $table->unsignedBigInteger('tag3_id')->nullable();
            $table->integer('priority_level');
            $table->string('color');
            $table->boolean('is_done')->default(false);
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
        Schema::dropIfExists('todos');
    }
};
