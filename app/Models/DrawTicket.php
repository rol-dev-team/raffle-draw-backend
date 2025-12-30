<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrawTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'is_winner',
    ];
}
