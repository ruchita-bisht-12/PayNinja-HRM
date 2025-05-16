<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRolesCompanyId extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
        });
    }
}
