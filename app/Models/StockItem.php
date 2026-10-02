<?php

namespace App\Models;

use App\Core\Model;

class StockItem extends Model
{
    protected string $table = 'stock_items';
    protected array $fillable = [
        'category_id',
        'location_id',
        'stock_code',
        'name',
        'brand',
        'model',
        'unit',
        'current_stock',
        'min_stock',
        'max_stock',
        'description'
    ];
}
