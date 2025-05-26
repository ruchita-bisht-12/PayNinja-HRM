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
            $table->boolean('enable_geolocation')->default(false)->after('track_location');
            $table->decimal('office_latitude', 10, 8)->nullable()->after('enable_geolocation');
            $table->decimal('office_longitude', 11, 8)->nullable()->after('office_latitude');
            $table->integer('geofence_radius')->default(100)->comment('In meters')->after('office_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_geolocation',
                'office_latitude',
                'office_longitude',
                'geofence_radius'
            ]);
        });
    }
};
