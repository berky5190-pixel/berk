<?php

return [
    'name' => getenv('APP_NAME') ?: 'Demirbaş ve Zimmet Yönetim Sistemi',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => (bool)(getenv('APP_DEBUG') ?: true),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Istanbul',
    'locale' => 'tr',
    'version' => '1.0.0',
];
