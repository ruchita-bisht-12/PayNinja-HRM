<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['Check In', 'Check Out', 'Status Update', 'Correction']);
            $table->dateTime('timestamp');
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_logs');
    }
};
