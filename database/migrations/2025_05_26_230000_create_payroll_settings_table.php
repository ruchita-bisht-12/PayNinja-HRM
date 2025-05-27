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
        Schema::create('payroll_config', function (Blueprint $table) {
            $table->id();
            $table->json('payment_methods');
            $table->string('default_payment_method');
            $table->json('tax_settings');
            $table->json('deduction_settings');
            $table->json('allowance_settings');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('payroll_config')->insert([
            'payment_methods' => json_encode(['bank_transfer', 'check', 'cash']),
            'default_payment_method' => 'bank_transfer',
            'tax_settings' => json_encode([
                'tax_percentage' => 5,
                'tax_id' => null,
            ]),
            'deduction_settings' => json_encode([
                'employee_contribution' => 12,
                'employer_contribution' => 12,
            ]),
            'allowance_settings' => json_encode([
                [
                    'name' => 'Transport',
                    'amount' => 0,
                    'is_taxable' => false,
                ],
                [
                    'name' => 'Meal',
                    'amount' => 0,
                    'is_taxable' => false,
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_config');
    }
};
