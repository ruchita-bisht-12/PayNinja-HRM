<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->enum('previous_status', ['Present', 'Absent', 'Late', 'On Leave', 'Half Day'])->nullable();
            $table->enum('new_status', ['Present', 'Absent', 'Late', 'On Leave', 'Half Day']);
            $table->dateTime('previous_check_in')->nullable();
            $table->dateTime('new_check_in')->nullable();
            $table->dateTime('previous_check_out')->nullable();
            $table->dateTime('new_check_out')->nullable();
            $table->text('reason');
            $table->foreignId('requested_by')->constrained('users');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
