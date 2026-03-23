<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // ── Règles communes ────────────────────────────────────────────────────
        $rules = [
            'role'      => ['required', 'in:particulier,professionnel,b2b'],
            'prenom'    => ['required', 'string', 'max:100'],
            'nom'       => ['required', 'string', 'max:100'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username', 'regex:/^[a-zA-Z0-9_.\-]+$/'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'adresse'   => ['required', 'string', 'max:255'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'photo_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cgv'       => ['accepted'],
        ];

        // ── Règles supplémentaires si pro ou b2b ───────────────────────────────
        if (in_array($request->role, ['professionnel', 'b2b'])) {
            $rules['societe_nom']     = ['required', 'string', 'max:255'];
            $rules['siret']           = ['required', 'string', 'size:14', 'regex:/^[0-9]{14}$/'];
            $rules['societe_adresse'] = ['required', 'string', 'max:255'];
        }

        $messages = [
            'username.regex'  => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, _, . et -',
            'siret.size'      => 'Le SIRET doit contenir exactement 14 chiffres',
            'siret.regex'     => 'Le SIRET ne doit contenir que des chiffres',
            'cgv.accepted'    => 'Vous devez accepter les conditions générales d\'utilisation',
            'password.min'    => 'Le mot de passe doit contenir au moins 8 caractères',
        ];

        $request->validate($rules, $messages);

        // ── Photo de profil ────────────────────────────────────────────────────
        $photoProfil = 'photos_profil/default.jpg';
        if ($request->hasFile('photo_profil')) {
            $photoProfil = $request->file('photo_profil')
                ->store('photos_profil', 'public');
        }

        // ── Création du user ───────────────────────────────────────────────────
        $user = User::create([
            'prenom'      => $request->prenom,
            'nom'         => $request->nom,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'adresse'     => $request->adresse,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'telephone'   => $request->telephone,
            'photo_profil'=> $photoProfil,

            // Champs pro uniquement
            'societe_nom'    => in_array($request->role, ['professionnel', 'b2b']) ? $request->societe_nom    : null,
            'siret'          => in_array($request->role, ['professionnel', 'b2b']) ? $request->siret          : null,
            'societe_adresse'=> in_array($request->role, ['professionnel', 'b2b']) ? $request->societe_adresse: null,
        ]);

        event(new Registered($user));
          try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\BienvenueBiocolis($user));
        } catch (\Exception $e) {
            \Log::warning('Email bienvenue échoué: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Bienvenue sur Biocolis ! Vérifiez votre email pour activer votre compte. 🌱');
    }
}
