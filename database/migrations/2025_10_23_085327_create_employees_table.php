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
    Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('employee_id', 20)->unique();
    $table->string('position');
    $table->enum('department', ['operations', 'management', 'customer_service', 'maintenance', 'delivery']);
    $table->date('hire_date');
    $table->decimal('salary', 10, 2)->nullable();
    $table->decimal('hourly_rate', 8, 2)->nullable();
    $table->json('work_schedule')->nullable();
    $table->json('permissions')->nullable();
    $table->integer('performance_rating')->nullable();
    $table->date('last_review_date')->nullable();
    $table->enum('employment_status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone', 20)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
    $table->index('department');
    $table->index('employment_status');
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
