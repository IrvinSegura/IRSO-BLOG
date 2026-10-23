<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToCanonicalHost
{
    /**
     * Send page visits to the scheme, host and port in APP_URL, so cookies
     * and OAuth callback URLs always use one address (e.g. 127.0.0.1 -> localhost).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = parse_url((string) config('app.url'));

        // Only GET and HEAD are redirected; redirecting a form post would lose its data.
        if (! isset($canonical['host']) || ! $request->isMethodSafe()) {
            return $next($request);
        }

        $scheme = $canonical['scheme'] ?? 'http';
        $port = $canonical['port'] ?? ($scheme === 'https' ? 443 : 80);

        if ($request->getScheme() === $scheme && $request->getHost() === $canonical['host'] && (int) $request->getPort() === $port) {
            return $next($request);
        }

        $url = $scheme.'://'.$canonical['host'].(isset($canonical['port']) ? ':'.$port : '').$request->getRequestUri();

        return redirect()->away($url, app()->isProduction() ? 301 : 302);
    }
}
