<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add geolocation fields for check-in
            $table->decimal('check_in_latitude', 10, 8)->nullable()->after('check_in_location');
            $table->decimal('check_in_longitude', 11, 8)->nullable()->after('check_in_latitude');
            
            // Add geolocation fields for check-out
            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('check_out_location');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude',
                'check_in_longitude',
                'check_out_latitude',
                'check_out_longitude'
            ]);
        });
    }
};
