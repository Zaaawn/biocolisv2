<?php
// ── Nouvel avis reçu (pour le vendeur) ────────────────────────────────────────
class NouvelAvis extends Notification {
    use Queueable;
    public function __construct(public \App\Models\Rating $rating) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        $etoiles = str_repeat('⭐', $this->rating->note);
        return [
            'type'    => 'nouvel_avis',
            'titre'   => '⭐ Nouvel avis',
            'message' => "{$this->rating->auteur->prenom} vous a laissé {$etoiles}",
            'url'     => route('stripe.dashboard'),
        ];
    }
}