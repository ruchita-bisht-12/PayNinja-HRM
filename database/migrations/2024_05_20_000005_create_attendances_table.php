<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('check_in_location', 255)->nullable();
            $table->string('check_out_location', 255)->nullable();
            $table->enum('status', ['Present', 'Absent', 'Late', 'On Leave', 'Half Day'])->default('Absent');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
