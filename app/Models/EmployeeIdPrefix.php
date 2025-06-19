<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIdPrefix extends Model
{
    use HasFactory;

    protected $table = 'employee_id_prefixes';

    protected $fillable = [
        'prefix',
        'padding',
        'start',
        'company_id',
        'employment_type',
    ];
}
