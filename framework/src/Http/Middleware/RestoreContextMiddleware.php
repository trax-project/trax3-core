<?php

namespace Trax\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Trax\Framework\Context;

class RestoreContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $contextHeader = $request->headers->get('TRAX_CONTEXT');

        // Some service request may not have the context header.
        // This is the case for the 'check' URLs.
        if (!empty($contextHeader)) {
            Context::restoreFromHeader($contextHeader);
        }

        return $next($request);
    }
}
