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
        Schema::create('income_expenditure', function (Blueprint $table) {
            $table->id('id');
            $table->tinyInteger('type')->default(0); // 0: Income, 1: Expenditure
            $table->decimal('amount_money', 10, 2);
            $table->date('transaction_date');
            $table->date('description')->nullable();
            $table->foreignId('performers')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_expenditure');
    }
};