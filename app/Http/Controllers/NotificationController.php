<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Liste toutes les notifications ────────────────────────────────────────
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        // Marquer toutes comme lues
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    // ── Compteur non-lues (pour le badge header) ──────────────────────────────
    public function compteur()
    {
        return response()->json([
            'total' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    // ── Marquer une notification comme lue ────────────────────────────────────
    public function lire(string $id)
    {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        $url = $notif->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    // ── Marquer toutes comme lues ─────────────────────────────────────────────
    public function toutLire()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // ── Supprimer une notification ────────────────────────────────────────────
    public function supprimer(string $id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();
        return back()->with('success', 'Notification supprimée.');
    }

    // ── Dernières notifs pour le dropdown header ──────────────────────────────
    public function dernieres()
    {
        $notifs = Auth::user()
            ->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'titre'   => $n->data['titre'] ?? '',
                'message' => $n->data['message'] ?? '',
                'url'     => $n->data['url'] ?? '#',
                'lu'      => !is_null($n->read_at),
                'date'    => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifs,
            'nb_non_lus'    => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
