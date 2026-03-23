<?php namespace App\Notifications;
use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

// ── Commande payée (pour l'acheteur) ──────────────────────────────────────────
class CommandePayee extends Notification {
    use Queueable;
    public function __construct(public Commande $commande) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        return [
            'type'    => 'commande_payee',
            'titre'   => '✅ Commande confirmée',
            'message' => "Votre commande {$this->commande->numero} est confirmée. Le vendeur va préparer votre colis.",
            'url'     => route('commandes.show', $this->commande->id),
        ];
    }
}