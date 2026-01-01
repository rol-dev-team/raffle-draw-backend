<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'prize_id',
        'prize_name',
        'category',
        'assigned_to',
        'draw_timestamp',
    ];

    protected $dates = [
        'draw_timestamp',
        'created_at',
        'updated_at',
    ];
}
