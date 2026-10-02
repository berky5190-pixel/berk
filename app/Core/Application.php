<?php

namespace App\Core;

use App\Helpers\SessionHelper;
use Throwable;

class Application
{
    private static ?Application $instance = null;
    private Router $router;
    private Request $request;
    private Response $response;
    private array $config;

    public function __construct()
    {
        self::$instance = $this;
        $this->loadEnvironment();
        $this->config = require dirname(__DIR__, 2) . '/config/app.php';

        $this->bootstrap();

        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
    }

    public static function getInstance(): Application
    {
        return self::$instance ?? new self();
    }

    private function loadEnvironment(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv("{$name}={$value}");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }

    private function bootstrap(): void
    {
        date_default_timezone_set($this->config['timezone'] ?? 'Europe/Istanbul');
        SessionHelper::start();

        if ($this->config['debug']) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        set_exception_handler([$this, 'handleException']);
    }

    public function handleException(Throwable $e): void
    {
        $isDebug = $this->config['debug'] ?? false;
        $statusCode = ($e->getCode() >= 400 && $e->getCode() <= 599) ? (int)$e->getCode() : 500;

        // Log error
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (is_dir($logDir) && is_writable($logDir)) {
            $logMsg = sprintf(
                "[%s] %s in %s:%d\nStack Trace:\n%s\n\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            file_put_contents($logDir . '/error.log', $logMsg, FILE_APPEND);
        }

        if (isset($this->request) && $this->request->isAjax()) {
            $this->response->setStatusCode($statusCode)->json([
                'status' => 'error',
                'message' => $isDebug ? $e->getMessage() : 'Bir sistem hatası meydana geldi.',
                'trace' => $isDebug ? $e->getTrace() : null
            ]);
            return;
        }

        $errorHtml = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sistem Hatası - ' . ($isDebug ? htmlspecialchars($e->getMessage()) : '500') . '</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 32px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { color: #ef4444; font-size: 24px; margin-top: 0; }
        pre { background: #0f172a; padding: 16px; border-radius: 8px; overflow-x: auto; color: #cbd5e1; font-size: 13px; line-height: 1.5; }
        .btn { display: inline-block; background: #3b82f6; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; margin-top: 20px; }
        .btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Sistem Hatası</h1>
        <p>' . ($isDebug ? htmlspecialchars($e->getMessage()) : 'Beklenmeyen bir hata oluştu. Lütfen sistem yöneticisiyle iletişime geçiniz.') . '</p>
        ' . ($isDebug ? '<p><strong>Dosya:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>' : '') . '
        <a href="/" class="btn">Ana Sayfaya Dön</a>
    </div>
</body>
</html>';

        $this->response->setStatusCode($statusCode)->html($errorHtml);
    }

    public function run(): void
    {
        // Load Web Routes
        $router = $this->router;
        $routesPath = dirname(__DIR__, 2) . '/routes/web.php';
        if (file_exists($routesPath)) {
            require $routesPath;
        }

        $this->router->dispatch($this->request, $this->response);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
