<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPayrollRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            // Add missing columns
            $table->integer('present_days')->default(0)->after('notes');
            $table->integer('leave_days')->default(0)->after('present_days');
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('leave_days');
            $table->decimal('overtime_amount', 12, 2)->default(0)->after('overtime_hours');
            $table->decimal('incentives', 12, 2)->default(0)->after('overtime_amount');
            $table->decimal('bonus', 12, 2)->default(0)->after('incentives');
            $table->decimal('advance_salary', 12, 2)->default(0)->after('bonus');
            $table->unsignedBigInteger('created_by')->nullable()->after('advance_salary');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn([
                'present_days',
                'leave_days',
                'overtime_hours',
                'overtime_amount',
                'incentives',
                'bonus',
                'advance_salary',
                'created_by',
                'updated_by'
            ]);
        });
    }
}
