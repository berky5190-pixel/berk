<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\SessionHelper;

class RoleMiddleware implements Middleware
{
    private array $allowedRoles;

    public function __construct(string ...$roles)
    {
        $this->allowedRoles = $roles;
    }

    public function handle(Request $request, callable $next): mixed
    {
        if (!AuthService::check()) {
            $response = new Response();
            return $response->redirect('/login');
        }

        if (!empty($this->allowedRoles) && !AuthService::hasRole($this->allowedRoles)) {
            if ($request->isAjax()) {
                $response = new Response();
                return $response->setStatusCode(403)->json([
                    'status' => 'error',
                    'message' => 'Bu işlem için yetkiniz bulunmamaktadır (403 Forbidden).'
                ]);
            }

            SessionHelper::setFlash('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            $response = new Response();
            return $response->redirect('/');
        }

        return $next($request);
    }
}
