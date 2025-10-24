<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->string('payment_number', 50)->unique();
    $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'check', 'digital_wallet']);
    $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
    $table->string('transaction_id')->nullable();
    $table->string('reference_number')->nullable();
    $table->timestamp('payment_date');
    $table->json('metadata')->nullable();
    $table->timestamps();
    $table->index('invoice_id');
    $table->index('payment_method');
    $table->index('status');
    $table->index('payment_date');
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
