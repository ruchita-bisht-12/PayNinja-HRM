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
        Schema::create('employee_beneficiary_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('beneficiary_badge_id')->constrained('beneficiary_badges')->onDelete('cascade');
            $table->decimal('custom_value', 10, 2)->nullable(); // Overrides badge default value if set
            $table->enum('custom_calculation_type', ['flat', 'percentage'])->nullable(); // Overrides badge default calculation type
            $table->string('custom_based_on')->nullable(); // Overrides badge default based_on
            $table->boolean('is_applicable')->default(true); // To enable/disable a badge for this specific employee
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'beneficiary_badge_id'], 'employee_badge_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_beneficiary_badges');
    }
};
