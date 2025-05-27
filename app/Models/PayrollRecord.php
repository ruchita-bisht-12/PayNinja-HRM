<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PayrollRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'payment_date',
        'basic_salary',
        'hra',
        'da',
        'other_allowances',
        'gross_salary',
        'pf_deduction',
        'esi_deduction',
        'professional_tax',
        'tds',
        'leave_deductions',
        'late_attendance_deductions',
        'other_deductions',
        'net_salary',
        'status',
        'notes',
        'present_days',
        'leave_days',
        'overtime_hours',
        'overtime_amount',
        'incentives',
        'bonus',
        'advance_salary',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'pf_deduction' => 'decimal:2',
        'esi_deduction' => 'decimal:2',
        'professional_tax' => 'decimal:2',
        'tds' => 'decimal:2',
        'leave_deductions' => 'decimal:2',
        'late_attendance_deductions' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'present_days' => 'integer',
        'leave_days' => 'integer',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'incentives' => 'decimal:2',
        'bonus' => 'decimal:2',
        'advance_salary' => 'decimal:2',
    ];

    protected $dates = [
        'pay_period_start',
        'pay_period_end',
        'payment_date',
        'deleted_at',
    ];

    // Status constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the statuses for the payroll record.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get the employee that owns the payroll record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who created the payroll record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the payroll record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the formatted payment date.
     */
    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date ? $this->payment_date->format('M d, Y') : 'Not paid';
    }

    /**
     * Get the formatted pay period.
     */
    public function getPayPeriodAttribute()
    {
        return $this->pay_period_start->format('M d') . ' - ' . $this->pay_period_end->format('M d, Y');
    }

    /**
     * Get the total deductions.
     */
    public function getTotalDeductionsAttribute()
    {
        return $this->pf_deduction + $this->esi_deduction + $this->professional_tax + 
               $this->tds + $this->leave_deductions + $this->late_attendance_deductions + 
               $this->other_deductions;
    }

    /**
     * Get the total earnings.
     */
    public function getTotalEarningsAttribute()
    {
        return $this->basic_salary + $this->hra + $this->da + $this->other_allowances + 
               $this->overtime_amount + $this->incentives + $this->bonus;
    }
}
