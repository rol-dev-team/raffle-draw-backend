<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'employee_id',
        'ticket_no',
        'ticket_type',
        'issue_date',
        'expire_date',
        'price',
        'status',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
