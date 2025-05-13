<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'description', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
