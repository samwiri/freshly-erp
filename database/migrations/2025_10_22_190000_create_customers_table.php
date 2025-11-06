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
    Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('customer_code', 20)->unique()->nullable();
    $table->string('company_name')->nullable();
    $table->string('email')->unique();
    $table->string('phone', 20)->nullable();
    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('zip', 10)->nullable();
    $table->string('tax_id')->nullable();
    $table->string('country')->nullable()->default('uganda');
    $table->enum('customer_type', ['individual', 'business'])->default('individual');
    $table->text('billing_address')->nullable();
    $table->text('delivery_address')->nullable();
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone', 20)->nullable();
    $table->integer('loyalty_points')->default(0);
    $table->enum('loyalty_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
    $table->decimal('lifetime_value', 10, 2)->default(0);
    $table->integer('total_orders')->default(0);
    $table->timestamp('last_order_date')->nullable();
    $table->json('preferences')->nullable();
    $table->json('tags')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->index('loyalty_tier');
    $table->index('customer_type');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};;
