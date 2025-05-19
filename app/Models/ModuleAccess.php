<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_access';

    protected $fillable = [
        'company_id',
        'module_name',
        'role',
        'has_access'
    ];

    protected $casts = [
        'has_access' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
