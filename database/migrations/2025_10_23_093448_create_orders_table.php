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
    Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number', 50)->unique();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
    $table->enum('service_type', ['wash', 'dry_clean', 'express', 'ironing', 'alterations']);
    $table->enum('status', ['received', 'washing', 'drying', 'ironing', 'ready', 'delivered', 'cancelled'])->default('received');
    $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
    $table->decimal('weight', 8, 2)->nullable();
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2)->default(0);
    $table->text('delivery_address')->nullable();
    $table->timestamp('pickup_time')->nullable();
    $table->timestamp('delivery_time')->nullable();
    $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
    $table->text('special_instructions')->nullable();
    $table->json('status_history')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->index('order_number');
    $table->index('customer_id');
    $table->index('status');
    $table->index('service_type');
    $table->index(['created_at', 'status']);
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
