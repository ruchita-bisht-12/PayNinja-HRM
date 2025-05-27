<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySettingsTable extends Migration
{
    public function up()
    {
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('basic_salary_percentage', 5, 2)->default(50.00);
            $table->decimal('hra_percentage', 5, 2)->default(30.00);
            $table->decimal('da_percentage', 5, 2)->default(10.00);
            $table->decimal('other_allowances_percentage', 5, 2)->default(10.00);
            $table->decimal('pf_percentage', 5, 2)->default(12.00);
            $table->decimal('esi_percentage', 5, 2)->default(1.75);
            $table->decimal('tds_percentage', 5, 2)->default(0.00);
            $table->decimal('professional_tax', 10, 2)->default(200.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_settings');
    }
}
