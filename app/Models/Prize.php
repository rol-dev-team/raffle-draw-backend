<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'is_drawn',

    ];
}
