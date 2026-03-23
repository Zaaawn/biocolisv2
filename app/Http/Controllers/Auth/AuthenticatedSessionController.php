<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // ── Compte banni ───────────────────────────────────────────────────────
        if ($user->is_banned) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => "Votre compte a été suspendu. Motif : {$user->ban_reason}"]);
        }

        // ── Compte inactif ─────────────────────────────────────────────────────
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte est désactivé. Contactez le support.']);
        }

        // ── Redirection selon rôle ─────────────────────────────────────────────
        return redirect()->intended($this->redirectionParRole($user->role));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }

    private function redirectionParRole(string $role): string
    {
        return match ($role) {
            'admin'         => route('dashboard'),
            'b2b'           => route('dashboard'),
            'professionnel' => route('dashboard'),
            default         => route('annonces.index'),
        };
    }
}
