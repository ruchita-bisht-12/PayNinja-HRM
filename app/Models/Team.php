<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'department_id',
        'name',
        'description',
        'created_by'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(Employee::class, 'team_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Helper methods to get reporters and reportees
    public function reporters()
    {
        return $this->members()
                    ->wherePivot('role', 'reporter');
    }

    public function reportees()
    {
        return $this->members()
                    ->wherePivot('role', 'reportee');
    }

    // Scope for company-specific teams
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Scope for department-specific teams
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
