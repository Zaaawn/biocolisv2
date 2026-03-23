<?php namespace App\Mail;
use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommandeStatutChange extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public Commande $commande, public string $statut) {}
    public function envelope(): Envelope {
        $labels = ['en_preparation'=>'En préparation','prete'=>'Prête','en_livraison'=>'En livraison','livree'=>'Livrée ✅'];
        return new Envelope(subject: '📦 Commande ' . ($labels[$this->statut] ?? $this->statut) . ' — ' . $this->commande->numero);
    }
    public function content(): Content {
        return new Content(view: 'emails.commandes.statut-change');
    }
}
