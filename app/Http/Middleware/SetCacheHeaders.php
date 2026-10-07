<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply caching to static assets
        $path = $request->path();
        
        // Images - cache for 1 year
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        
        // CSS and JavaScript - cache for 1 month
        elseif (preg_match('/\.(css|js)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
        }
        
        // Fonts - cache for 1 year
        elseif (preg_match('/\.(ttf|otf|woff|woff2|eot)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        return $response;
    }
}
