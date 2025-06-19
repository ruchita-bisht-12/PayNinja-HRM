<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing AttendanceSetting records to include alternate Saturdays if not present
        $rows = DB::table('attendance_settings')->get();
        foreach ($rows as $row) {
            $days = [];
            if (is_string($row->weekend_days)) {
                $decoded = json_decode($row->weekend_days, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $days = $decoded;
                }
            } elseif (is_array($row->weekend_days)) {
                $days = $row->weekend_days;
            }
            // Add alternate Saturday keys if not present
            $alts = ['saturday_1_3', 'saturday_2_4', 'saturday_1_3_5'];
            $updated = false;
            foreach ($alts as $alt) {
                if (!in_array($alt, $days, true)) {
                    $days[] = $alt;
                    $updated = true;
                }
            }
            if ($updated) {
                DB::table('attendance_settings')
                    ->where('id', $row->id)
                    ->update(['weekend_days' => json_encode($days)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove alternate Saturday keys from all AttendanceSetting records
        $alts = ['saturday_1_3', 'saturday_2_4', 'saturday_1_3_5'];
        $rows = DB::table('attendance_settings')->get();
        foreach ($rows as $row) {
            $days = [];
            if (is_string($row->weekend_days)) {
                $decoded = json_decode($row->weekend_days, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $days = $decoded;
                }
            } elseif (is_array($row->weekend_days)) {
                $days = $row->weekend_days;
            }
            $newDays = array_values(array_diff($days, $alts));
            if (count($newDays) !== count($days)) {
                DB::table('attendance_settings')
                    ->where('id', $row->id)
                    ->update(['weekend_days' => json_encode($newDays)]);
            }
        }
    }
};
