<?php

namespace App\Models;

use App\Core\Model;

class Department extends Model
{
    protected string $table = 'departments';
    protected array $fillable = ['name', 'code', 'description', 'status'];
}
