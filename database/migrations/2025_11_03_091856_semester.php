<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->year('school_year'); // Năm học
            $table->tinyInteger('semester'); // Học kỳ (1 hoặc 2)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};