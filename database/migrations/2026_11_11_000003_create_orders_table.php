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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('order_type', ['takeaway', 'delivery'])->default('takeaway');
            $table->string('customer_name')->nullable();
            $table->string('customer_wa')->nullable();
            $table->string('delivery_area')->nullable();
            $table->integer('delivery_fee')->default(0);
            $table->integer('total_amount');
            $table->enum('payment_method', ['cash', 'qris'])->default('cash');
            $table->integer('cash_received')->nullable();
            $table->integer('change_amount')->nullable();
            $table->enum('status', ['masuk', 'dibuat', 'selesai'])->default('masuk');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
