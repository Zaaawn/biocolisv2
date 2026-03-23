<?php namespace App\Mail;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauMessageEmail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(
        public User $destinataire,
        public User $expediteur,
        public Conversation $conversation
    ) {}
    public function envelope(): Envelope {
        return new Envelope(subject: '💬 Nouveau message de ' . $this->expediteur->prenom . ' sur Biocolis');
    }
    public function content(): Content {
        return new Content(view: 'emails.messages.nouveau-message');
    }
}
