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
    Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->string('item_type');
    $table->text('description')->nullable();
    $table->integer('quantity')->default(1);
    $table->decimal('unit_price', 8, 2)->default(0);
    $table->decimal('total_price', 10, 2)->default(0);
    $table->text('special_instructions')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->index('order_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
