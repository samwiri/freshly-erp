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
        Schema::table('orders', function (Blueprint $table) {
            // Make service_type nullable since it's not validated in the controller
            $table->enum('service_type', ['wash', 'dry_clean', 'express', 'ironing', 'alterations'])->nullable()->change();
            
            // Make status nullable (will keep default 'received' but allow null)
            $table->enum('status', ['received', 'washing', 'drying', 'ironing', 'ready', 'delivered', 'cancelled'])->nullable()->change();
            
            // Make priority nullable (will keep default 'medium' but allow null)
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert service_type to not nullable
            $table->enum('service_type', ['wash', 'dry_clean', 'express', 'ironing', 'alterations'])->nullable(false)->change();
            
            // Revert status to not nullable with default
            $table->enum('status', ['received', 'washing', 'drying', 'ironing', 'ready', 'delivered', 'cancelled'])->default('received')->nullable(false)->change();
            
            // Revert priority to not nullable with default
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->nullable(false)->change();
        });
    }
};
