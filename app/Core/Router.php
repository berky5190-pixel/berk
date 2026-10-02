<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): self
    {
        $prefix = '';
        $groupMiddlewares = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $groupMws = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $groupMiddlewares = array_merge($groupMiddlewares, $groupMws);
            }
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        if ($fullPath !== '/' && str_ends_with($fullPath, '/')) {
            $fullPath = rtrim($fullPath, '/');
        }

        $allMiddlewares = array_merge($groupMiddlewares, $middlewares);

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middlewares' => $allMiddlewares,
            'pattern' => $this->compilePattern($fullPath)
        ];

        return $this;
    }

    private function compilePattern(string $path): string
    {
        // Replace {param} or {param:[regex]} with regex capture groups
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/', function ($matches) {
            $paramName = $matches[1];
            $regex = $matches[2] ?? '[^/]+';
            return "(?P<{$paramName}>{$regex})";
        }, $path);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request, Response $response): mixed
    {
        $requestMethod = $request->getMethod();
        $requestUri = $request->getUri();

        $matchedRoute = null;
        $params = [];
        $methodAllowed = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                if ($route['method'] === $requestMethod) {
                    $matchedRoute = $route;
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }
                    break;
                } else {
                    $methodAllowed = true;
                }
            }
        }

        if (!$matchedRoute) {
            if ($methodAllowed) {
                return $response->setStatusCode(405)->json([
                    'status' => 'error',
                    'message' => '405 Method Not Allowed'
                ]);
            }
            return $response->setStatusCode(404)->json([
                'status' => 'error',
                'message' => '404 Sayfa veya Uç Nokta Bulunamadı',
                'uri' => $requestUri
            ]);
        }

        // Middleware Pipeline execution
        $middlewares = $matchedRoute['middlewares'];
        $handler = $matchedRoute['handler'];

        $pipeline = function ($req) use ($handler, $params, $response) {
            if (is_callable($handler)) {
                return call_user_func_array($handler, array_merge([$req, $response], $params));
            }

            if (is_array($handler) && count($handler) === 2) {
                [$controllerClass, $method] = $handler;
                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Controller sınıfı bulunamadı: {$controllerClass}");
                }
                $controller = new $controllerClass($req, $response);
                if (!method_exists($controller, $method)) {
                    throw new \RuntimeException("Controller metodu bulunamadı: {$controllerClass}@{$method}");
                }
                return call_user_func_array([$controller, $method], $params);
            }

            throw new \RuntimeException('Geçersiz rota işleyicisi (handler).');
        };

        // Wrap middlewares in reverse order
        while ($middlewareClass = array_pop($middlewares)) {
            $pipeline = function ($req) use ($middlewareClass, $pipeline) {
                if (!class_exists($middlewareClass)) {
                    throw new \RuntimeException("Middleware bulunamadı: {$middlewareClass}");
                }
                $middlewareInstance = new $middlewareClass();
                return $middlewareInstance->handle($req, $pipeline);
            };
        }

        return $pipeline($request);
    }
}
