<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * First-touch campaign attribution: the first time a session sees a
 * `utm_source` query param, stash it (plus the landing URL) in the session
 * and never overwrite it on a later visit in the same session. Read back by
 * PricingController::store() when a PendingSignup is created.
 */
class CaptureUtmParameters
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('utm_source') && ! $request->session()->has('utm_attribution')) {
            $request->session()->put('utm_attribution', [
                'utm_source'   => $request->query('utm_source'),
                'utm_medium'   => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'utm_term'     => $request->query('utm_term'),
                'utm_content'  => $request->query('utm_content'),
                'landing_page' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}
