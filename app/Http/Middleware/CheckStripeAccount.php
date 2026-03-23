<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que le compte Stripe du vendeur est actif avant d'accéder aux ventes
 */
class CheckStripeAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->stripe_account_status !== 'actif') {
            return redirect()->route('stripe.onboarding')
                ->with('warning', 'Vous devez compléter votre compte Stripe pour recevoir des paiements.');
        }

        return $next($request);
    }
}

