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
        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('shifts', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('description');
            }
            if (!Schema::hasColumn('shifts', 'is_night_shift')) {
                $table->boolean('is_night_shift')->default(false)->after('is_default');
            }
            if (!Schema::hasColumn('shifts', 'has_break')) {
                $table->boolean('has_break')->default(false)->after('is_night_shift');
            }
            if (!Schema::hasColumn('shifts', 'break_start')) {
                $table->time('break_start')->nullable()->after('has_break');
            }
            if (!Schema::hasColumn('shifts', 'break_end')) {
                $table->time('break_end')->nullable()->after('break_start');
            }
            if (!Schema::hasColumn('shifts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('shifts', 'is_default')) {
                $columnsToDrop[] = 'is_default';
            }
            if (Schema::hasColumn('shifts', 'is_night_shift')) {
                $columnsToDrop[] = 'is_night_shift';
            }
            if (Schema::hasColumn('shifts', 'has_break')) {
                $columnsToDrop[] = 'has_break';
            }
            if (Schema::hasColumn('shifts', 'break_start')) {
                $columnsToDrop[] = 'break_start';
            }
            if (Schema::hasColumn('shifts', 'break_end')) {
                $columnsToDrop[] = 'break_end';
            }
            if (Schema::hasColumn('shifts', 'deleted_at')) {
                $columnsToDrop[] = 'deleted_at';
            }
            
            if (count($columnsToDrop) > 0) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
