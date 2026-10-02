<?php

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function sendHeaders(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }
    }

    public function html(string $content, int $status = 200): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->sendHeaders();
        echo $content;
        exit;
    }

    public function json(mixed $data, int $status = 200): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function redirect(string $url, int $status = 302): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Location', $url);
        $this->sendHeaders();
        exit;
    }

    public function download(string $filePath, ?string $downloadName = null, string $mimeType = 'application/octet-stream', bool $inline = false): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->setStatusCode(404)->html('Dosya bulunamadı.');
        }

        $filename = $downloadName ?: basename($filePath);
        $disposition = $inline ? 'inline' : 'attachment';

        $this->setStatusCode(200);
        $this->setHeader('Content-Description', 'File Transfer');
        $this->setHeader('Content-Type', $mimeType);
        $this->setHeader('Content-Disposition', "{$disposition}; filename=\"{$filename}\"");
        $this->setHeader('Content-Transfer-Encoding', 'binary');
        $this->setHeader('Expires', '0');
        $this->setHeader('Cache-Control', 'private, must-revalidate, post-check=0, pre-check=0');
        $this->setHeader('Pragma', 'public');
        $this->setHeader('Content-Length', (string)filesize($filePath));
        $this->setHeader('X-Content-Type-Options', 'nosniff');

        $this->sendHeaders();

        // Clear output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        readfile($filePath);
        exit;
    }
}
