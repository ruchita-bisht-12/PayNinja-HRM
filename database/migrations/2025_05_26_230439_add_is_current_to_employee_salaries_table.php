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
            if (!Schema::hasColumn('employee_salaries', 'is_current')) {
                $table->boolean('is_current')->default(false)->after('status');
                
                // Add index for better performance
                $table->index(['employee_id', 'is_current']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salaries', function (Blueprint $table) {
            if (Schema::hasColumn('employee_salaries', 'is_current')) {
                // Drop the index first
                $table->dropIndex(['employee_id', 'is_current']);
                // Then drop the column
                $table->dropColumn('is_current');
            }
        });
    }
};
