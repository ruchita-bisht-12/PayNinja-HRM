<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->time('auto_absent_time')->default('11:00:00');
            $table->boolean('allow_multiple_check_in')->default(false);
            $table->boolean('track_location')->default(false);
            $table->string('weekend_days')->default('Saturday,Sunday');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_settings');
    }
};
