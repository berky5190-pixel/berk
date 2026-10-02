<?php

return [
    'root' => dirname(__DIR__) . '/storage',
    'uploads_path' => dirname(__DIR__) . '/storage/uploads',
    'assignments_path' => dirname(__DIR__) . '/storage/uploads/assignments',
    'employees_path' => dirname(__DIR__) . '/storage/uploads/employees',
    'assets_path' => dirname(__DIR__) . '/storage/uploads/assets',
    'generated_docs_path' => dirname(__DIR__) . '/storage/generated_docs',
    'logs_path' => dirname(__DIR__) . '/storage/logs',

    'max_upload_size' => (int)(getenv('STORAGE_MAX_UPLOAD_SIZE_MB') ?: 10) * 1024 * 1024, // 10MB in bytes

    'allowed_assignment_mimes' => [
        'application/pdf' => ['pdf'],
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
    ],

    'allowed_image_mimes' => [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
    ],
];
