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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Relationships
            $table->foreignId('user_id');
            $table->foreignId('trainer_id')->nullable();
            $table->foreignId('gym_id')->nullable();
            $table->foreignId('session_id');
            $table->string("payment_id")->nullable();
            // booking info
            $table->date('booking_date');
            $table->string('time_slot');
            // Status: 0 = cancelled, 1 = confirmed, 2 = pending
            $table->enum('status', ['0', '1', '2'])->default('1');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
