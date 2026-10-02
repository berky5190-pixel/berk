<?php

namespace App\Models;

use App\Core\Model;

class AssetRequest extends Model
{
    protected string $table = 'asset_requests';
    protected array $fillable = [
        'request_code',
        'user_id',
        'employee_id',
        'request_type',
        'asset_id',
        'title',
        'description',
        'urgency',
        'status',
        'admin_notes',
        'resolved_by_user_id',
        'resolved_at'
    ];

    public static function generateCode(): string
    {
        return 'TLP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }
}
