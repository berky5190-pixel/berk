<?php

namespace App\Models;

use App\Core\Model;

class Employee extends Model
{
    protected string $table = 'employees';
    protected array $fillable = [
        'department_id',
        'registration_no',
        'first_name',
        'last_name',
        'email',
        'phone',
        'title',
        'hire_date',
        'status',
        'photo_path',
        'notes'
    ];
}
