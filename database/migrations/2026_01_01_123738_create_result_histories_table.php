<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_histories', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->unsignedBigInteger('prize_id');
            $table->string('prize_name');
            $table->string('category');
            $table->string('assigned_to')->nullable();
            $table->timestamp('draw_timestamp')->nullable();
            $table->timestamps();

            // Optional: add foreign key if you have prizes table
            // $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_histories');
    }
};
