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
           
            if (!Schema::hasColumn('order_items', 'service_item_id')) {
                $table->foreignId('service_item_id')
                    ->nullable()
                    ->after('order_id')
                    ->constrained('service_items')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key & column
            if (Schema::hasColumn('order_items', 'service_item_id')) {
                $table->dropForeign(['service_item_id']);
                $table->dropColumn('service_item_id');
            }
        });
    }
};
