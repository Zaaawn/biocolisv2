<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // ── INDEX — Liste toutes les conversations ─────────────────────────────────
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with([
                'annonce',
                'acheteur',
                'vendeur',
                'dernierMessage.sender',
            ])
            ->pourUser($userId)
            ->nonArchivees($userId)
            ->orderByDesc('dernier_message_at')
            ->paginate(20);

        // Nombre total de non-lus
        $totalNonLus = Conversation::pourUser($userId)
            ->get()
            ->sum('non_lus_pour_moi');

        return view('messages.index', compact('conversations', 'totalNonLus'));
    }

    // ── SHOW — Affiche une conversation ───────────────────────────────────────
    public function show(Annonce $annonce, User $user)
    {
        $authId  = Auth::id();
        $vendeur = $annonce->user;

        // Déterminer acheteur et vendeur
        if ($authId === $vendeur->id) {
            // Je suis le vendeur, $user est l'acheteur
            $acheteurId = $user->id;
            $vendeurId  = $authId;
        } else {
            // Je suis l'acheteur
            $acheteurId = $authId;
            $vendeurId  = $vendeur->id;
        }

        // Créer ou récupérer la conversation
        $conversation = Conversation::firstOrCreate(
            [
                'annonce_id'  => $annonce->id,
                'acheteur_id' => $acheteurId,
                'vendeur_id'  => $vendeurId,
            ],
            [
                'dernier_message_at' => now(),
            ]
        );

        // Vérifier que l'utilisateur est bien participant
        abort_unless(
            $conversation->acheteur_id === $authId ||
            $conversation->vendeur_id  === $authId,
            403
        );

        // Marquer comme lus
        $conversation->marquerCommeLu($authId);

        // Charger les messages
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        $autreParticipant = $conversation->autre_participant;

        return view('messages.show', compact(
            'conversation', 'messages', 'annonce', 'autreParticipant'
        ));
    }

    // ── ENVOYER un message ────────────────────────────────────────────────────
       public function envoyer(Request $request)
    {
        try {
            $data = $request->validate([
                'conversation_id' => ['required', 'exists:conversations,id'],
                'contenu'         => ['nullable', 'string', 'max:2000'],
                'images'          => ['nullable', 'array', 'max:4'],
                'images.*'        => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            ]);
 
            $conversation = Conversation::findOrFail($data['conversation_id']);
            abort_unless($conversation->acheteur_id === Auth::id() || $conversation->vendeur_id === Auth::id(), 403);
 
            if (empty($data['contenu']) && !$request->hasFile('images')) {
                return response()->json(['success' => false, 'message' => 'Écrivez un message ou ajoutez une image.'], 422);
            }
 
            $dernierMsg = Message::where('sender_id', Auth::id())->where('conversation_id', $conversation->id)->latest()->first();
            if ($dernierMsg && $dernierMsg->created_at->diffInSeconds(now()) < 3) {
                return response()->json(['success' => false, 'message' => 'Patientez avant d\'envoyer un nouveau message.'], 429);
            }
 
            $imagesPaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $imagesPaths[] = $img->store('messages/' . $conversation->id, 'public');
                }
            }
 
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => Auth::id(),
                'contenu'         => !empty($data['contenu']) ? e($data['contenu']) : null,
                'images'          => $imagesPaths ?: null,
                'type'            => ($imagesPaths && empty($data['contenu'])) ? 'image' : 'texte',
                'is_read'         => false,
            ]);
 
            $message->load('sender');
 
            // Notifier le destinataire
            $destinataire = $conversation->acheteur_id === Auth::id()
                ? $conversation->vendeur
                : $conversation->acheteur;
 
            $destinataire?->notify(new \App\Notifications\NouveauMessage($conversation, Auth::user()));
 
            // ✅ Email si inactif depuis 30+ minutes
            if ($destinataire && $destinataire->updated_at->diffInMinutes(now()) > 30) {
                try {
                    Mail::to($destinataire->email)
                        ->send(new \App\Mail\NouveauMessageEmail($destinataire, Auth::user(), $conversation));
                } catch (\Exception $e) {
                    \Log::warning('Email message échoué: ' . $e->getMessage());
                }
            }
 
            $html = view('messages.partials.message', compact('message'))->render();
 
            return response()->json([
                'success'  => true,
                'message'  => $html,
                'id'       => $message->id,
                'nb_total' => $conversation->fresh()->nb_messages,
            ]);
 
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => implode(' ', $e->errors()[array_key_first($e->errors())])], 422);
        } catch (\Exception $e) {
            \Log::error('Envoyer message error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    // ── POLLING — Nouveaux messages depuis un ID ───────────────────────────────
    public function polling(Request $request, Conversation $conversation)
    {
        abort_unless(
            $conversation->acheteur_id === Auth::id() ||
            $conversation->vendeur_id  === Auth::id(),
            403
        );

        $depuisId = $request->input('depuis_id', 0);

        $nouveaux = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $depuisId)
            ->where('sender_id', '!=', Auth::id()) // seulement les messages des autres
            ->orderBy('created_at')
            ->get();

        // Marquer comme lus
        if ($nouveaux->isNotEmpty()) {
            $conversation->marquerCommeLu(Auth::id());
        }

        $html = $nouveaux->map(function ($message) {
            return view('messages.partials.message', compact('message'))->render();
        })->join('');

        return response()->json([
            'success'   => true,
            'html'      => $html,
            'dernier_id'=> $nouveaux->last()?->id ?? $depuisId,
            'nb_nouveaux' => $nouveaux->count(),
        ]);
    }

    // ── POLLING CONVERSATIONS — Badge non-lus dans le header ──────────────────
    public function nonLus()
    {
        $total = Conversation::pourUser(Auth::id())
            ->get()
            ->sum('non_lus_pour_moi');

        return response()->json(['total' => $total]);
    }

    // ── ARCHIVER une conversation ──────────────────────────────────────────────
    public function archiver(Conversation $conversation)
    {
        abort_unless(
            $conversation->acheteur_id === Auth::id() ||
            $conversation->vendeur_id  === Auth::id(),
            403
        );

        $userId = Auth::id();
        if ($conversation->acheteur_id === $userId) {
            $conversation->update(['archive_acheteur' => true]);
        } else {
            $conversation->update(['archive_vendeur' => true]);
        }

        return back()->with('success', 'Conversation archivée.');
    }

    // ── SUPPRIMER un message (soft delete, seulement le sien) ─────────────────
    public function supprimerMessage(Message $message)
    {
        abort_unless($message->sender_id === Auth::id(), 403);

        // On peut supprimer seulement dans les 10 minutes
        abort_unless($message->created_at->diffInMinutes(now()) <= 10, 403);

        $message->delete();

        return response()->json(['success' => true]);
    }

    // ── DÉMARRER conversation depuis une annonce ───────────────────────────────
    public function demarrer(Annonce $annonce)
    {
        // Pas sa propre annonce
        abort_unless($annonce->user_id !== Auth::id(), 403);

        return redirect()->route('messages.show', [
            'annonce' => $annonce->id,
            'user'    => $annonce->user_id,
        ]);
    }
}