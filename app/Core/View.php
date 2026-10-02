<?php

namespace App\Core;

use RuntimeException;

class View
{
    private static string $viewsPath;
    private array $data = [];
    private ?string $layout = 'main';

    public function __construct()
    {
        self::$viewsPath = dirname(__DIR__) . '/Views';
    }

    public function setLayout(?string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function render(string $view, array $data = []): string
    {
        $this->data = array_merge($this->data, $data);
        
        $viewFile = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("Görünüm dosyası bulunamadı: {$viewFile}");
        }

        // Extract variables to view scope
        extract($this->data, EXTR_SKIP);

        // Capture inner view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // If no layout is specified, return raw content
        if ($this->layout === null) {
            return $content;
        }

        $layoutFile = self::$viewsPath . '/layouts/' . $this->layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Şablon (layout) dosyası bulunamadı: {$layoutFile}");
        }

        // Capture layout wrapped content
        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }

    public static function partial(string $partial, array $data = []): string
    {
        $file = self::$viewsPath . '/' . str_replace('.', '/', $partial) . '.php';
        if (!file_exists($file)) {
            return '';
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim(getenv('APP_URL') ?: '', '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    public static function asset(string $path = ''): string
    {
        return self::url('/assets/' . ltrim($path, '/'));
    }
}
