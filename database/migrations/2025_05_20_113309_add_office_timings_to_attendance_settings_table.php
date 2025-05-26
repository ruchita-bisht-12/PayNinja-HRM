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
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->time('office_start_time')->default('09:00:00')->after('weekend_days');
            $table->time('office_end_time')->default('18:00:00')->after('office_start_time');
            $table->integer('work_hours')->default(8)->after('office_end_time');
            $table->time('grace_period')->default('00:15:00')->after('work_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn(['office_start_time', 'office_end_time', 'work_hours', 'grace_period']);
        });
    }
};
