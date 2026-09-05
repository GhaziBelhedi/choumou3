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
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_email', 191)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            // Snapshot de l'adresse de livraison au moment de la commande
            $table->string('shipping_full_name', 150);
            $table->string('shipping_phone', 20);
            $table->string('shipping_governorate', 100);
            $table->string('shipping_city', 100);
            $table->string('shipping_address_line', 255);
            $table->string('shipping_postal_code', 10)->nullable();

            $table->text('customer_notes')->nullable();
            $table->enum('payment_method', ['cod'])->default('cod');
            $table->string('cancellation_reason', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
