<?php namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenueBiocolis extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public User $user) {}
    public function envelope(): Envelope {
        return new Envelope(subject: '🌱 Bienvenue sur Biocolis, ' . $this->user->prenom . ' !');
    }
    public function content(): Content {
        return new Content(view: 'emails.auth.bienvenue');
    }
}
