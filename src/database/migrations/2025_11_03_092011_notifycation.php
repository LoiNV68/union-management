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
        Schema::create('notifycation', function (Blueprint $table) {
            $table->id('id');
            $table->string('title');
            $table->longText('content');
            $table->date('date_sent');
            $table->foreignId('sender')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('notify_type', [0, 1])->default(0); // 0: all, 1: specific
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifycation');
    }
};