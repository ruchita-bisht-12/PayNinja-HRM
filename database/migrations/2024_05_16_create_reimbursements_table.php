<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('receipt_path')->nullable();
            $table->enum('status', ['pending', 'reporter_approved', 'admin_approved', 'rejected'])->default('pending');
            
            // Reporter approval fields
            $table->text('reporter_remarks')->nullable();
            $table->foreignId('reporter_id')->nullable()->constrained('team_members', 'employee_id')->onDelete('set null');
            $table->timestamp('reporter_approved_at')->nullable();
            
            // Admin approval fields
            $table->text('admin_remarks')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('team_members', 'employee_id')->onDelete('set null');
            $table->timestamp('admin_approved_at')->nullable();
            
            // Rejection fields
            $table->text('remarks')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reimbursements');
    }
};
