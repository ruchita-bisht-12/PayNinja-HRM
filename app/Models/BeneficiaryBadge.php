<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeneficiaryBadge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'calculation_type',
        'value',
        'based_on',
        'company_id',
        'is_active',
        'is_company_wide',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'is_company_wide' => 'boolean',
    ];

    /**
     * Get the company that owns the beneficiary badge.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee beneficiary badge assignments for this badge.
     */
    public function employeeBeneficiaryBadges()
    {
        return $this->hasMany(EmployeeBeneficiaryBadge::class);
    }

    /**
     * The employees that are assigned this beneficiary badge.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_beneficiary_badges')
                    ->using(EmployeeBeneficiaryBadge::class)
                    ->withPivot([
                        'id',
                        'custom_value', 
                        'custom_calculation_type', 
                        'custom_based_on', 
                        'is_applicable', 
                        'start_date', 
                        'end_date',
                        'created_at',
                        'updated_at'
                    ])
                    ->withTimestamps();
    }
    
    /**
     * Apply this badge to all employees in the company
     *
     * @param array $pivotAttributes Additional attributes to set on the pivot table
     * @return int Number of employees the badge was applied to
     */
    public function applyToAllEmployees(array $pivotAttributes = [])
    {
        if (!$this->company_id) {
            throw new \RuntimeException('Cannot apply badge: No company associated with this badge');
        }
        
        // Get all employee IDs that don't already have this badge
        $employeeIds = Employee::where('company_id', $this->company_id)
            ->whereDoesntHave('beneficiaryBadges', function($query) {
                $query->where('beneficiary_badges.id', $this->id);
            })
            ->pluck('id');
            
        if ($employeeIds->isEmpty()) {
            return 0;
        }
        
        // Prepare the data for bulk insert
        $now = now();
        $data = $employeeIds->map(function($employeeId) use ($pivotAttributes, $now) {
            return array_merge([
                'employee_id' => $employeeId,
                'beneficiary_badge_id' => $this->id,
                'is_applicable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], $pivotAttributes);
        })->toArray();
        
        // Bulk insert
        return \DB::table('employee_beneficiary_badges')->insert($data) ? count($data) : 0;
    }
}
