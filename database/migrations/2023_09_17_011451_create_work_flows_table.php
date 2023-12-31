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
        Schema::create('work_flows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name');
            $table->dateTime('deadline');
            $table->string('memo')->nullable();
            $table->integer('contents_num')->default(0);
            $table->unsignedBigInteger('tag1_id')->nullable();
            $table->unsignedBigInteger('tag2_id')->nullable();
            $table->unsignedBigInteger('tag3_id')->nullable();
            $table->string('color');
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
        Schema::dropIfExists('work_flows');
    }
};
