<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    protected array $fillable = ['key', 'value', 'group', 'description'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $model = new self();
        $record = $model->findBy('key', $key);
        return $record ? $record['value'] : $default;
    }
}
