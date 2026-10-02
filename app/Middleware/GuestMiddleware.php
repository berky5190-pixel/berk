<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class GuestMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (AuthService::check()) {
            $response = new Response();
            return $response->redirect('/');
        }

        return $next($request);
    }
}
