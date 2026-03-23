<?php 
// ── Statut commande changé (pour l'acheteur) ──────────────────────────────────
class StatutCommandeChange extends Notification {
    use Queueable;
    public function __construct(public Commande $commande, public string $nouveauStatut) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        $labels = [
            'en_preparation' => ['👨‍🍳', 'En cours de préparation'],
            'prete'          => ['📦', 'Prête à être récupérée'],
            'en_livraison'   => ['🚴', 'En cours de livraison'],
            'livree'         => ['✅', 'Livrée'],
        ];
        [$ico, $label] = $labels[$this->nouveauStatut] ?? ['📋', ucfirst($this->nouveauStatut)];
        return [
            'type'    => 'statut_commande',
            'titre'   => "{$ico} Commande mise à jour",
            'message' => "Votre commande {$this->commande->numero} est maintenant : {$label}",
            'url'     => route('commandes.show', $this->commande->id),
        ];
    }
}