<?php

namespace App\Core;

use App\Helpers\SessionHelper;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $viewEngine = new View();
        if ($layout !== 'main') {
            $viewEngine->setLayout($layout);
        }
        
        $html = $viewEngine->render($view, array_merge([
            'currentUser' => SessionHelper::get('user'),
            'flash' => SessionHelper::getFlashes(),
        ], $data));

        $this->response->html($html);
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): void
    {
        $this->response->redirect($url, $statusCode);
    }

    protected function back(): void
    {
        $referer = $this->request->header('referer', '/');
        $this->redirect($referer);
    }

    protected function flash(string $type, string $message): void
    {
        SessionHelper::setFlash($type, $message);
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        $sanitized = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                if (str_contains($rule, ':')) {
                    [$ruleName, $ruleParam] = explode(':', $rule, 2);
                }

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || trim((string)$value) === '') {
                            $errors[$field][] = "{$field} alanı zorunludur.";
                        }
                        break;
                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "Geçerli bir e-posta adresi giriniz.";
                        }
                        break;
                    case 'min':
                        if ($value && mb_strlen((string)$value) < (int)$ruleParam) {
                            $errors[$field][] = "En az {$ruleParam} karakter olmalıdır.";
                        }
                        break;
                    case 'max':
                        if ($value && mb_strlen((string)$value) > (int)$ruleParam) {
                            $errors[$field][] = "En fazla {$ruleParam} karakter olabilir.";
                        }
                        break;
                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "Sayısal bir değer olmalıdır.";
                        }
                        break;
                }
            }

            $sanitized[$field] = is_string($value) ? trim($value) : $value;
        }

        if (!empty($errors)) {
            if ($this->request->isAjax()) {
                $this->json(['status' => 'error', 'errors' => $errors], 422);
            }
            SessionHelper::setFlash('errors', $errors);
            SessionHelper::setFlash('old', $data);
            $this->back();
        }

        return $sanitized;
    }
}
