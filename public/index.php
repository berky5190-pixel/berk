<?php

declare(strict_types=1);

/**
 * Demirbaş ve Zimmet Yönetim Sistemi
 * Front Controller Entry Point
 */

define('APP_START', microtime(true));
define('ROOT_PATH', dirname(__DIR__));

// 1. Autoloading: Composer or Built-in PSR-4 Fallback
$composerAutoload = ROOT_PATH . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    // Built-in PSR-4 Autoloader for App\ namespace
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = ROOT_PATH . '/app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// 2. Initialize and Run Application
try {
    $app = new App\Core\Application();
    $app->run();
} catch (Throwable $e) {
    // Top level fallback if Application constructor itself fails
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>Kritik Sistem Başlatma Hatası</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
