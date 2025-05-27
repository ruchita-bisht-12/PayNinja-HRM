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
        // Add all new columns in a single batch
        Schema::table('employee_salaries', function (Blueprint $table) {
            // Allowances
            $table->decimal('conveyance_allowance', 12, 2)->default(0)->after('other_allowances');
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->decimal('food_allowance', 12, 2)->default(0);
            $table->decimal('travel_allowance', 12, 2)->default(0);
            
            // Bonuses
            $table->decimal('performance_bonus', 12, 2)->default(0);
            $table->decimal('attendance_bonus', 12, 2)->default(0);
            
            // Leave related
            $table->decimal('leave_encashment', 12, 2)->default(0);
            $table->decimal('leave_deductions', 12, 2)->default(0);
            $table->json('leave_balance')->nullable();
            
            // Other deductions
            $table->decimal('professional_development', 12, 2)->default(0);
            $table->decimal('union_fees', 12, 2)->default(0);
            $table->decimal('insurance_premium', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            
            // Tax related
            $table->enum('tax_regime', ['old', 'new'])->default('new');
            $table->json('tax_exemptions')->nullable();
            $table->json('section_wise_deductions')->nullable();
            
            // Reimbursements
            $table->decimal('reimbursement_amount', 12, 2)->default(0);
            $table->json('reimbursement_details')->nullable();
            
            // Payment info
            $table->enum('payment_frequency', ['monthly', 'bi-weekly', 'weekly', 'semi-monthly'])->default('monthly');
            $table->string('payment_reference')->nullable();
            $table->date('payment_date')->nullable();
            
            // Overtime
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_rate', 12, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
        });
        
        // Add indexes in a separate statement
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->index('payment_date');
            $table->index('payment_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndexIfExists('employee_salaries_payment_date_index');
            $table->dropIndexIfExists('employee_salaries_payment_frequency_index');
            
            // Drop columns in chunks to avoid row size limits
            $columns = [
                'conveyance_allowance', 'medical_allowance', 'special_allowance',
                'food_allowance', 'travel_allowance', 'performance_bonus',
                'attendance_bonus', 'leave_encashment', 'leave_deductions',
                'leave_balance', 'professional_development', 'union_fees',
                'insurance_premium', 'other_deductions', 'tax_regime',
                'tax_exemptions', 'section_wise_deductions', 'reimbursement_amount',
                'reimbursement_details', 'payment_frequency', 'payment_reference',
                'payment_date', 'overtime_hours', 'overtime_rate', 'overtime_amount'
            ];
            
            // Drop columns in chunks
            foreach (array_chunk($columns, 10) as $chunk) {
                $table->dropColumn($chunk);
            }
        });
    }
};
