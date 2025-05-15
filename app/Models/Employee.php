<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'designation_id',
        'name',
        'email',
        'phone',
        'dob',
        'gender',
        'emergency_contact',
        'joining_date',
        'employment_type',
        'address',
        'avatar',
        'created_by',
        'updated_by',
        'status'
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function employeeDetail()
    {
        return $this->hasOne(EmployeeDetail::class, 'user_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Helper methods
    public function isReporterInTeam(Team $team)
    {
        return $this->teams()
                    ->wherePivot('team_id', $team->id)
                    ->wherePivot('role', 'reporter')
                    ->exists();
    }

    public function isReporteeInTeam(Team $team)
    {
        return $this->teams()
                    ->wherePivot('team_id', $team->id)
                    ->wherePivot('role', 'reportee')
                    ->exists();
    }
}
