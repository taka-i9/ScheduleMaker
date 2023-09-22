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
        Schema::create('work_flow_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('workflow_id')->index();
            $table->unsignedBigInteger('start_id')->index();
            $table->unsignedBigInteger('end_id')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workflow_id')->references('id')->on('work_flows')->onDelete('cascade');
            $table->foreign('start_id')->references('id')->on('work_flow_contents')->onDelete('cascade');
            $table->foreign('end_id')->references('id')->on('work_flow_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_flow_links');
    }
};
