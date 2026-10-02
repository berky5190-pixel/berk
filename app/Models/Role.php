<?php

namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected string $table = 'roles';
    protected array $fillable = ['name', 'slug', 'description'];
}
