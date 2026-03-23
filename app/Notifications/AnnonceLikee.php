

<?php
// ── Annonce likée ─────────────────────────────────────────────────────────────
class AnnonceLikee extends Notification {
    use Queueable;
    public function __construct(
        public \App\Models\Annonce $annonce,
        public \App\Models\User $liker
    ) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        return [
            'type'    => 'annonce_likee',
            'titre'   => '❤️ Quelqu\'un aime votre annonce',
            'message' => "{$this->liker->prenom} a ajouté \"{$this->annonce->titre}\" à ses favoris.",
            'url'     => route('annonces.show', $this->annonce->slug),
        ];
    }
}
