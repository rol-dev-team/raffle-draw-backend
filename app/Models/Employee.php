<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'branch',
        'division',
        'reg_code',
        'name',
        'department',
        'designation',
        'company',
        'gender',
    ];
}
