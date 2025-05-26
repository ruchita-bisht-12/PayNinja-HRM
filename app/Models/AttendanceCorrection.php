<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'previous_status',
        'new_status',
        'previous_check_in',
        'new_check_in',
        'previous_check_out',
        'new_check_out',
        'reason',
        'requested_by',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'previous_check_in' => 'datetime',
        'new_check_in' => 'datetime',
        'previous_check_out' => 'datetime',
        'new_check_out' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
