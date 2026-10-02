<?php

namespace App\Models;

use App\Core\Model;

class Asset extends Model
{
    protected string $table = 'assets';
    protected array $fillable = [
        'category_id',
        'location_id',
        'asset_code',
        'name',
        'brand',
        'model',
        'serial_number',
        'barcode',
        'purchase_date',
        'purchase_price',
        'currency',
        'warranty_start',
        'warranty_end',
        'status',
        'photo_path',
        'description',
        'created_by'
    ];
}
