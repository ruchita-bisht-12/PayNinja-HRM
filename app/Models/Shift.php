<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'description',
        'is_default',
        'is_night_shift',
        'has_break',
        'break_start',
        'break_end',
        'is_active'
    ];
    
    protected $attributes = [
        'is_default' => false,
        'is_night_shift' => false,
        'has_break' => false,
        'is_active' => true,
        'grace_period_minutes' => 15,
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'is_night_shift' => 'boolean',
        'has_break' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Scope a query to only include default shifts.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    /**
     * Scope a query to only include active shifts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get the shift duration in hours.
     */
    public function getDurationInHoursAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        // If it's a night shift and end time is next day
        if ($this->is_night_shift && $end->lt($start)) {
            $end->addDay();
        }
        
        // Subtract break time if exists
        $breakMinutes = 0;
        if ($this->has_break && $this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->break_start);
            $breakEnd = Carbon::parse($this->break_end);
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
        }
        
        $totalMinutes = $start->diffInMinutes($end) - $breakMinutes;
        return round($totalMinutes / 60, 2);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function departmentShifts()
    {
        return $this->hasMany(DepartmentShift::class);
    }

    public function designationShifts()
    {
        return $this->hasMany(DesignationShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
