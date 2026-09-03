<?php

namespace CrudBooster\CacheBooster;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Optimize
{
    protected $skipCachePaths = [
        'auth/login',
        'auth/logout',
        'auth/forgot',
        'auth/password-reset',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $skipCache = fn() => collect($this->skipCachePaths)->contains(fn($path) => $request->is($path));

        // Catch post request, and increase the cache version
        if ($request->isMethod('POST') && config('cb.cache_booster.enabled') && !$skipCache()) {
            if(Cache::has('cache_version')) {
                Cache::increment('cache_version');
            } else {
                Cache::put('cache_version', 1);
            }
        }

        if ($response instanceof Response && !$response instanceof StreamedResponse && config('cb.cache_booster.enabled') && !$skipCache()) {
            $content = $response->getContent();
            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('Pragma', 'cache');
            $response->headers->set('Expires', gmdate(DATE_RFC1123, time() + 60 * config('cb.cache_booster.expiry', 5)));

            // Append all a href with version ?v=
           $content = preg_replace_callback('/<a[^>]+href=([\'"])(?<href>http[^\'"]+?)\1[^>]*>/i', function ($match) {
               $href = $match['href'];
               if (!str_contains($href, '?v=')) {
                   if (!str_contains($href, '?')) {
                       $href .= '?v=' . Cache::get('cache_version', 1);
                   } else {
                       $href .= '&v=' . Cache::get('cache_version', 1);
                   }
               }
               return str_replace($match['href'], $href, $match[0]);
           }, $content);

            // Minify HTML remove new line 2 or more
            $content = preg_replace('/\n\s*\n/', "\n", $content);

            $response->setContent($content);
        }

        // Catch the redirect response and append the cache version
        if ($response->isRedirection() && config('cb.cache_booster.enabled') && !$skipCache()) {
            $response->setTargetUrl($response->getTargetUrl() . '?v=' . $this->getCacheKey());
        }

        return $response;
    }

    private function getCacheKey(): string
    {
        return Cache::get('cache_version', 1);
    }
}
