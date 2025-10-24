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
    Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number', 50)->unique();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->date('invoice_date');
    $table->date('due_date');
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2)->default(0);
    $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
    $table->string('payment_terms', 50)->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->index('customer_id');
    $table->index('status');
    $table->index('due_date');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
