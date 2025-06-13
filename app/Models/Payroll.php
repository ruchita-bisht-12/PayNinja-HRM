<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Added for soft delete functionality

class Payroll extends Model
{
    use HasFactory, SoftDeletes; // Added SoftDeletes

    protected $fillable = [
        'employee_id',
        'company_id',
        'pay_period_start',
        'pay_period_end',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
        'processed_by',
        'notes',
        'data_snapshot',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'data_snapshot' => 'array', // For storing JSON data
    ];

    /**
     * Get the employee that owns the payroll.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the company that this payroll belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who processed the payroll.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get all of the items for the payroll.
     */
    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    /**
     * Get the color for the status badge.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match (strtolower($this->status)) {
            'pending' => 'warning',
            'processed', 'generated' => 'info',
            'paid' => 'success',
            'cancelled' => 'secondary',
            'failed', 'error' => 'danger',
            default => 'primary',
        };
    }

    /**
     * Get the currency symbol for the payroll.
     *
     * @return string
     */
    public function getCurrencySymbolAttribute(): string
    {
        // Basic mapping, can be expanded or use a library for more comprehensive symbols
        return match (strtoupper($this->currency ?? 'USD')) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            // Add other common currencies
            default => $this->currency ?? '$', // Fallback to code or default symbol
        };
    }
}
