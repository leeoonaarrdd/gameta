<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DigiflazzService;
use Symfony\Component\HttpFoundation\Response;

class EnsureDigiflazzConfigured
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->digiflazzService->isConfigured()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap'
                ], 400);
            }

            return redirect()->route('admin.configuration.index')
                ->with('error', 'Konfigurasi Digiflazz belum lengkap. Silakan lengkapi konfigurasi terlebih dahulu.')
                ->with('active_tab', 'digiflazz');
        }

        return $next($request);
    }
}
