<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        if ($user->role !== $role) {
            // Redirect ke dashboard yang sesuai
            if ($user->role === 'pasien') {
                return redirect('/pasien')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($user->role === 'dokter') {
                return redirect('/dokter')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            } elseif ($user->role === 'admin') {
                return redirect('/admin')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
