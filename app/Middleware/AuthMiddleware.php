<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\SessionHelper;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (!AuthService::check()) {
            if ($request->isAjax()) {
                $response = new Response();
                return $response->setStatusCode(401)->json([
                    'status' => 'error',
                    'message' => 'Oturum açmanız gerekmektedir.'
                ]);
            }

            SessionHelper::setFlash('error', 'Lütfen önce giriş yapınız.');
            SessionHelper::set('intended_url', $request->getUri());

            $response = new Response();
            return $response->redirect('/login');
        }

        return $next($request);
    }
}
