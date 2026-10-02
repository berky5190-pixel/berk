<?php

namespace App\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\CsrfHelper;
use App\Helpers\SessionHelper;

class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): mixed
    {
        $method = $request->getMethod();

        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $request->input('_csrf')
                ?: $request->header('X_CSRF_TOKEN')
                ?: $request->header('CSRF_TOKEN');

            if (!CsrfHelper::validateToken($token)) {
                if ($request->isAjax()) {
                    $response = new Response();
                    return $response->setStatusCode(403)->json([
                        'status' => 'error',
                        'message' => 'CSRF güvenlik token doğrulaması başarısız (403 Forbidden).'
                    ]);
                }

                SessionHelper::setFlash('error', 'Oturum güvenlik anahtarı (CSRF) geçersiz veya süresi dolmuş. Lütfen formu tekrar gönderin.');
                $response = new Response();
                $referer = $request->header('referer', '/');
                return $response->redirect($referer);
            }
        }

        return $next($request);
    }
}
