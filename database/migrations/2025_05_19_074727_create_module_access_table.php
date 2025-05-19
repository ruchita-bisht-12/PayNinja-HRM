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
        Schema::create('module_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('module_name');
            $table->enum('role', ['admin', 'employee', 'reporter','company_admin','superadmin','user','reportee']);
            $table->boolean('has_access')->default(false);
            $table->timestamps();

            // Unique constraint to prevent duplicate entries
            $table->unique(['company_id', 'module_name', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_access');
    }
};
