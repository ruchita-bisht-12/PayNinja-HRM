<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeIdPrefixesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_id_prefixes', function (Blueprint $table) {
            $table->id();
            $table->string('prefix');
            $table->integer('padding');
            $table->integer('start');
            $table->unsignedBigInteger('company_id');
            $table->enum('employment_type', ['permanent', 'trainee']);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unique(['company_id', 'employment_type'], 'company_employment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_id_prefixes');
    }
}
