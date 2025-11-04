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
        Schema::create('members', function (Blueprint $table) {
            $table->id('id'); // khóa chính
            $table->string('full_name', 255);
            $table->date('birth_date');
            $table->enum('gender', [0, 1]); // 0: Nam, 1: Nữ,
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number', 20)->nullable();
            $table->date('join_date')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Inactive, 1: Active
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // liên kết tài khoản đăng nhập nếu có
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade'); // liên kết chi đoàn nếu có
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};