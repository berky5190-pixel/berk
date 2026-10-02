<?php

namespace App\Models;

use App\Core\Model;

class AssignmentDocument extends Model
{
    protected string $table = 'assignment_documents';
    protected array $fillable = [
        'assignment_id',
        'document_type',
        'original_filename',
        'stored_filename',
        'file_path',
        'mime_type',
        'file_size',
        'sha256_hash',
        'description',
        'uploaded_by_user_id'
    ];
}
