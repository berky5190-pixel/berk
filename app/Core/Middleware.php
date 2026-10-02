<?php

namespace App\Core;

interface Middleware
{
    /**
     * Handle the incoming request.
     * 
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, callable $next): mixed;
}
