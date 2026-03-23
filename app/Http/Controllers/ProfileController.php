<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ── Validation ─────────────────────────────────────────────────────────
        $rules = [
            'prenom'    => ['required', 'string', 'max:100'],
            'nom'       => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id, 'regex:/^[a-zA-Z0-9_.\-]+$/'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse'   => ['required', 'string', 'max:255'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        if ($user->isProfessionnel()) {
            $rules['societe_nom']              = ['nullable', 'string', 'max:255'];
            $rules['siret']                    = ['nullable', 'string', 'max:14'];
            $rules['societe_adresse']          = ['nullable', 'string', 'max:255'];
            $rules['tva_intracommunautaire']   = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules);

        // ── Photo de profil ────────────────────────────────────────────────────
        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne photo si pas la photo par défaut
            if ($user->photo_profil && !str_contains($user->photo_profil, 'PdpDefaut')) {
                Storage::disk('public')->delete($user->photo_profil);
            }
            $user->photo_profil = $request->file('photo_profil')
                ->store('photos_profil', 'public');
        }

        // ── Email modifié → reset vérification ────────────────────────────────
        if ($user->email !== $request->email) {
            $user->email_verified_at = null;
        }

        // ── Mise à jour des champs ─────────────────────────────────────────────
        $user->prenom    = $request->prenom;
        $user->nom       = $request->nom;
        $user->username  = $request->username;
        $user->email     = $request->email;
        $user->telephone = $request->telephone;
        $user->adresse   = $request->adresse;
        $user->latitude  = $request->latitude;
        $user->longitude = $request->longitude;

        if ($user->isProfessionnel()) {
            $user->societe_nom            = $request->societe_nom;
            $user->siret                  = $request->siret;
            $user->societe_adresse        = $request->societe_adresse;
            $user->tva_intracommunautaire = $request->tva_intracommunautaire;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('profile-updated', true);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password'      => ['required', 'current_password'],
            'password'              => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('password-updated', true);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil')
            ->with('success', 'Votre compte a été supprimé.');
    }
      public function updateIban(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'titulaire_compte' => ['required', 'string', 'max:255'],
            'iban'             => ['required', 'string', 'min:14', 'max:34', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/'],
            'bic'              => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
        ], [
            'iban.regex' => 'Format IBAN invalide. Exemple : FR7630006000011234567890189',
            'bic.regex'  => 'Format BIC invalide. Exemple : BNPAFRPP',
        ]);

        // Nettoyer l'IBAN (supprimer les espaces)
        $iban = strtoupper(preg_replace('/\s+/', '', $request->iban));

        // Ne pas sauvegarder si c'est le masque (••••xxxx)
        if (str_contains($iban, '•')) {
            return redirect()->route('profile.edit')
                ->with('iban-updated', true)
                ->withFragment('iban');
        }

        $request->user()->update([
            'titulaire_compte' => $request->titulaire_compte,
            'iban'             => $iban,
            'bic'              => strtoupper($request->bic ?? ''),
        ]);

        return redirect()->route('profile.edit')
            ->with('iban-updated', true)
            ->withFragment('iban');
    }
}
