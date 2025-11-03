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
        Schema::create('activity', function (Blueprint $table) {
            $table->id('id');
            $table->string('activity_name');
            $table->longText('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location');
            $table->enum('status', [0, 1])->default(0); // 0: Pending, 1: Approved
            $table->integer('max_participants');
            $table->foreignId('creator')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity');
    }
};