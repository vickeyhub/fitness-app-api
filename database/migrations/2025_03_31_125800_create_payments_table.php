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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('customer_id')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('payment_intent_id')->unique();
            $table->string('status');
            $table->integer('amount');
            $table->string('currency');
            $table->string('payment_method')->nullable();
            $table->json('response_data'); // Store full Stripe response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
