<?php
// ── Nouveau message (pour le destinataire) ────────────────────────────────────
class NouveauMessage extends Notification {
    use Queueable;
    public function __construct(
        public \App\Models\Conversation $conversation,
        public \App\Models\User $expediteur
    ) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): array {
        return [
            'type'    => 'nouveau_message',
            'titre'   => '💬 Nouveau message',
            'message' => "{$this->expediteur->prenom} vous a envoyé un message.",
            'url'     => route('messages.show', [
                'annonce' => $this->conversation->annonce_id,
                'user'    => $this->expediteur->id,
            ]),
        ];
    }
}