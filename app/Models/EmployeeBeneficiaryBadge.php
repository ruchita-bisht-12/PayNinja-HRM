<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBeneficiaryBadge extends Pivot
{
    /** @use HasFactory<\Database\Factories\EmployeeBeneficiaryBadgeFactory> */
    use HasFactory;

    protected $table = 'employee_beneficiary_badges';

    protected $fillable = [
        'employee_id',
        'beneficiary_badge_id',
        'custom_value',
        'custom_calculation_type',
        'custom_based_on',
        'is_applicable',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'custom_value' => 'decimal:2',
        'is_applicable' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the employee that owns this badge assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the beneficiary badge definition for this assignment.
     */
    public function beneficiaryBadge()
    {
        return $this->belongsTo(BeneficiaryBadge::class);
    }
}
