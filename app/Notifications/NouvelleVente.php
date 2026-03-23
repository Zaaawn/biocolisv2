<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleVente extends Notification
{
    use Queueable;

    public function __construct(public Commande $commande) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'nouvelle_vente',
            'titre'       => '🛒 Nouvelle vente !',
            'message'     => "Commande {$this->commande->numero} — " .
                             number_format($this->commande->montant_vendeur, 2) . "€ (après commission)",
            'url'         => route('commandes.show', $this->commande->id),
            'commande_id' => $this->commande->id,
            'montant'     => $this->commande->montant_vendeur,
        ];
    }
}
