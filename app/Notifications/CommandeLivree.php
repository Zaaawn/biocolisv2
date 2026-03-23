<?php
// ── Commande livrée (pour l'acheteur) ─────────────────────────────────────────
class CommandeLivree extends Notification {
    use Queueable;
    public function __construct(public Commande $commande) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        return [
            'type'    => 'commande_livree',
            'titre'   => '📦 Commande livrée',
            'message' => "Votre commande {$this->commande->numero} a été livrée. Confirmez la réception pour libérer le paiement.",
            'url'     => route('commandes.show', $this->commande->id),
        ];
    }
}
