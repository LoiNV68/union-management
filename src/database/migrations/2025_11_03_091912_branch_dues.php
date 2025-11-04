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
        Schema::create('branch_dues', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->decimal('amount_money', 12, 2);
            $table->tinyInteger('type')->default(0); // 0: Income, 1: Expenditure
            $table->date('payment_date')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_dues');
    }
};