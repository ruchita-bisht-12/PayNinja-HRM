<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'employee_id',
        'role',
        'assigned_by'
    ];

    protected $casts = [
        'role' => 'string',
    ];

    // Relationships
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Helper methods
    public function isReporter()
    {
        return $this->role === 'reporter';
    }

    public function isReportee()
    {
        return $this->role === 'reportee';
    }
}
