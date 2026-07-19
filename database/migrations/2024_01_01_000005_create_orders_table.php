<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // dipakai sebagai order_id di Midtrans
            $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('final_price', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'cancelled', 'expired'])->default('pending');
            $table->string('payment_method')->nullable(); // qris, bank_transfer, gopay, dll
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_proof_path')->nullable(); // fallback kalau transfer manual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
