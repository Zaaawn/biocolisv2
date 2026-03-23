<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'utilisateur n'est pas banni
 */
class CheckNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->is_banned) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => "Votre compte a été suspendu. Motif : {$user->ban_reason}"]);
        }

        return $next($request);
    }
}
