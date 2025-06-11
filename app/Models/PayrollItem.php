<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'type',
        'description',
        'amount',
        'is_taxable',
        'related_id', // For polymorphic relations
        'related_type', // For polymorphic relations
        'meta', // For storing additional structured data
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
        'meta' => 'json', // Cast meta to JSON
    ];

    /**
     * Get the payroll that owns the item.
     */
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    /**
     * Get the parent related model (e.g., Leave, Reimbursement, Loan record).
     */
    public function related()
    {
        return $this->morphTo();
    }
}
