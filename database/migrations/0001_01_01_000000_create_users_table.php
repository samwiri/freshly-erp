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
    Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'manager', 'employee', 'customer'])->default('customer');
    $table->string('phone', 20)->nullable();
    $table->text('address')->nullable();
    $table->string('avatar')->nullable();
    $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
    $table->timestamp('last_login_at')->nullable();
    $table->string('last_login_ip', 45)->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
    $table->index('email');
    $table->index('role');
    $table->index('status');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
