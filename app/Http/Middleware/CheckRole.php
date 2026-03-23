<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de contrôle des rôles
 *
 * Usage dans les routes :
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,professionnel')
 *   ->middleware('role:b2b,professionnel,admin')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles)) {
            abort(403, 'Accès refusé. Vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
