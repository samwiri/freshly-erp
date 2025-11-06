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
        Schema::table('order_items', function (Blueprint $table) {
            // Add service_type enum column to match what the controller is using
            $table->enum('service_type', ['wash', 'dry_clean', 'express', 'ironing', 'alterations'])->nullable()->after('item_type');
            
            // Add price column (alias for unit_price)
            $table->decimal('price', 8, 2)->nullable()->after('unit_price');
            
            // Make item_type nullable since service_type is being used instead
            $table->string('item_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'price']);
            $table->string('item_type')->nullable(false)->change();
        });
    }
};
