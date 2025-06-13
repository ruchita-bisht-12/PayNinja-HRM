<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAttendanceAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'amount',
        'description',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeHalfDays($query)
    {
        return $query->where('type', 'half_day');
    }

    public function scopeReimbursements($query)
    {
        return $query->where('type', 'reimbursement');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
