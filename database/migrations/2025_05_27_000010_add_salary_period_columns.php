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
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('effective_from');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('leaves_deduction', 10, 2)->default(0)->after('total_deductions');
            $table->timestamp('paid_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date',
                'leaves_deduction',
                'paid_at'
            ]);
        });
    }
};
