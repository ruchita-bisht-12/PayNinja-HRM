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
        Schema::create('beneficiary_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g., 'allowance', 'deduction'
            $table->string('calculation_type'); // 'flat' or 'percentage'
            $table->decimal('value', 12, 2);
            $table->string('based_on')->nullable(); // e.g., 'basic_salary', 'gross_salary'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_company_wide')->default(false);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_badges');
    }
};
