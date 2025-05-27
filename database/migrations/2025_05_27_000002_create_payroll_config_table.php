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
        if (!Schema::hasTable('payroll_config')) {
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
            $defaultSettings = [
                'payment_methods' => ['bank_transfer', 'check', 'cash'],
                'default_payment_method' => 'bank_transfer',
                'tax_settings' => [
                    'tax_percentage' => 10,
                    'tax_id' => null,
                ],
                'deduction_settings' => [
                    'employee_contribution' => 5,
                    'employer_contribution' => 10,
                ],
                'allowance_settings' => [
                    [
                        'name' => 'Transportation',
                        'amount' => 0,
                        'is_taxable' => false,
                    ],
                    [
                        'name' => 'Meal',
                        'amount' => 0,
                        'is_taxable' => false,
                    ],
                ],
            ];

            \DB::table('payroll_config')->insert([
                'payment_methods' => json_encode($defaultSettings['payment_methods']),
                'default_payment_method' => $defaultSettings['default_payment_method'],
                'tax_settings' => json_encode($defaultSettings['tax_settings']),
                'deduction_settings' => json_encode($defaultSettings['deduction_settings']),
                'allowance_settings' => json_encode($defaultSettings['allowance_settings']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_config');
    }
};
