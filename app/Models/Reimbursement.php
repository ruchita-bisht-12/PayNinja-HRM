<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reimbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'company_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'receipt_path',
        'status',
        'reporter_remarks',
        'admin_remarks',
        'reporter_id',
        'admin_id',
        'reporter_approved_at',
        'admin_approved_at',
        'remarks',
        'rejected_by',
        'rejected_at'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'reporter_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the employee that owns the reimbursement request.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the company that owns the reimbursement request.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the reporter who approved/rejected the request.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the admin who approved/rejected the request.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope a query to only include pending reimbursements.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include reporter approved reimbursements.
     */
    public function scopeReporterApproved($query)
    {
        return $query->where('status', 'reporter_approved');
    }

    /**
     * Scope a query to only include admin approved reimbursements.
     */
    public function scopeAdminApproved($query)
    {
        return $query->where('status', 'admin_approved');
    }

    /**
     * Scope a query to only include rejected reimbursements.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
