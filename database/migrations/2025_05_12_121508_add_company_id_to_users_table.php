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
        Schema::table('users', function (Blueprint $table) {
            // Add the company_id column and foreign key constraint
            $table->foreignId('company_id')
                  ->nullable()
                  ->after('id') // Place after the primary key column
                  ->constrained('companies')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
