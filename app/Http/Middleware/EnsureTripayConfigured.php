<?php

namespace App\Http\Middleware;

use App\Services\TripayService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTripayConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tripayService = new TripayService();
        
        if (!$tripayService->isConfigured()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ], 503);
            }
            
            return redirect()->back()->with('error', 'Payment gateway tidak dikonfigurasi. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
