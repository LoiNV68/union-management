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
        Schema::create('activity_registration', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('activity')->onDelete('cascade');
            $table->date('registration_time');
            $table->tinyInteger('registration_status')->default(0);
            $table->string('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registration');
    }
};