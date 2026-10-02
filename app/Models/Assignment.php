<?php

namespace App\Models;

use App\Core\Model;

class Assignment extends Model
{
    protected string $table = 'assignments';
    protected array $fillable = [
        'assignment_code',
        'employee_id',
        'asset_id',
        'assigned_by_user_id',
        'received_by_name',
        'assignment_date',
        'planned_return_date',
        'actual_return_date',
        'status',
        'return_condition',
        'return_notes',
        'notes'
    ];
}
