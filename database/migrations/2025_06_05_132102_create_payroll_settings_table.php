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
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->unique(); // Ensures one setting row per company
            $table->json('deductible_leave_type_ids')->nullable()->comment('Array of LeaveType IDs');
            $table->integer('late_arrival_threshold')->nullable()->comment('Number of late arrivals before deduction');
            $table->decimal('late_arrival_deduction_days', 8, 2)->nullable()->comment('Number of days to deduct for lateness threshold');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
