<?php

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $bodyParams;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        // Handle method override for PUT, DELETE via _method form field or header
        if ($this->method === 'POST') {
            if (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), ['PUT', 'PATCH', 'DELETE'])) {
                $this->method = strtoupper($_POST['_method']);
            } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }

        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $parsedUri = parse_url($rawUri, PHP_URL_PATH) ?? '/';
        $this->uri = '/' . trim($parsedUri, '/');
        if ($this->uri !== '/' && str_ends_with($this->uri, '/')) {
            $this->uri = rtrim($this->uri, '/');
        }

        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;
        $this->files = $_FILES;

        // If JSON payload is sent
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawBody = file_get_contents('php://input');
            $jsonData = json_decode($rawBody, true);
            if (is_array($jsonData)) {
                $this->bodyParams = array_merge($this->bodyParams, $jsonData);
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->queryParams;
        }
        return $this->queryParams[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return array_merge($this->queryParams, $this->bodyParams);
        }
        return $this->bodyParams[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->bodyParams;
        }
        return $this->bodyParams[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function isAjax(): bool
    {
        return (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($this->server['HTTP_ACCEPT']) && str_contains($this->server['HTTP_ACCEPT'], 'application/json'));
    }

    public function getIp(): string
    {
        if (!empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $normalizedKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$normalizedKey] ?? $this->server[strtoupper($key)] ?? $default;
    }
}
