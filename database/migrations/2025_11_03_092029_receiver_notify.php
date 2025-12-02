<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_receivers', function (Blueprint $table) {
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->primary(['notification_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_receivers');
    }
};