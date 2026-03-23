<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnonceOptionController;
use Illuminate\Support\Facades\Route;

// ── ACCUEIL ───────────────────────────────────────────────────────────────────
Route::get('/', [AccueilController::class, 'index'])->name('accueil');

// ── DASHBOARD ─────────────────────────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ══════════════════════════════════════════════════════════════════════════════
// ANNONCES
// ⚠️ IMPORTANT : routes STATIQUES avant routes DYNAMIQUES ({slug})
// Sinon /annonces/creer est intercepté par /annonces/{annonce:slug}
// ══════════════════════════════════════════════════════════════════════════════
// Avis
Route::post('/commandes/{commande}/avis',      [RatingController::class, 'store'])->name('ratings.store');
Route::patch('/avis/{rating}/repondre',        [RatingController::class, 'repondre'])->name('ratings.repondre');

// Page publique avis vendeur (sans auth)
Route::get('/vendeurs/{user}/avis',            [RatingController::class, 'vendeur'])->name('ratings.vendeur');

// Publiques
Route::get('/annonces', [AnnonceController::class, 'index'])->name('annonces.index');
Route::get('/annonces/autocomplete', [AnnonceController::class, 'autocomplete'])->name('annonces.autocomplete');

// ── SIGNALEMENT ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/signalements', [SignalementController::class, 'store'])->name('signalements.store');
    Route::get('/admin/signalements', [SignalementController::class, 'index'])->name('signalements.index');
    Route::patch('/admin/signalements/{signalement}', [SignalementController::class, 'traiter'])->name('signalements.traiter');
});
// ⚠️ Routes statiques AVANT {slug}
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/annonces/creer', [AnnonceController::class, 'create'])->name('annonces.create');
    Route::post('/annonces', [AnnonceController::class, 'store'])->name('annonces.store');
    Route::get('/mes-annonces', [AnnonceController::class, 'mesAnnonces'])->name('annonces.mes-annonces');
    Route::patch('/profil/iban', [ProfileController::class, 'updateIban'])->name('profile.iban');
});

// Routes dynamiques {slug} — APRÈS les statiques
Route::get('/annonces/{annonce:slug}', [AnnonceController::class, 'show'])->name('annonces.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/annonces/{annonce:slug}/modifier', [AnnonceController::class, 'edit'])->name('annonces.edit');
    Route::put('/annonces/{annonce:slug}', [AnnonceController::class, 'update'])->name('annonces.update');
    Route::delete('/annonces/{annonce:slug}', [AnnonceController::class, 'destroy'])->name('annonces.destroy');
    Route::post('/annonces/{annonce}/like', [AnnonceController::class, 'toggleLike'])->name('annonces.like');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/annonces/{annonce:slug}/boost',  [AnnonceOptionController::class, 'boost'])->name('options.boost');
    Route::post('/annonces/{annonce:slug}/boost', [AnnonceOptionController::class, 'payer'])->name('options.payer');
    Route::get('/options/success',               [AnnonceOptionController::class, 'success'])->name('options.success');
});

Route::get('/tarifs', [AbonnementController::class, 'tarifs'])->name('abonnements.tarifs');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/abonnements/{plan}', [AbonnementController::class, 'souscrire'])->name('abonnements.souscrire');
    Route::get('/abonnements/success', [AbonnementController::class, 'success'])->name('abonnements.success');
    Route::get('/mon-abonnement', [AbonnementController::class, 'monAbonnement'])->name('abonnements.mon-abonnement');
    Route::patch('/abonnements/{abonnement}/annuler', [AbonnementController::class, 'annuler'])->name('abonnements.annuler');
});
// ══════════════════════════════════════════════════════════════════════════════
// PANIER
// ⚠️ Routes statiques (index, checkout, compteur, vider) AVANT /{ligne}
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->prefix('panier')->name('panier.')->group(function () {
    Route::get('/',          [PanierController::class, 'index'])->name('index');
    Route::get('/checkout',  [PanierController::class, 'checkout'])->name('checkout');
    Route::get('/compteur',  [PanierController::class, 'compteur'])->name('compteur');
    Route::delete('/',       [PanierController::class, 'vider'])->name('vider');

    // ⚠️ ajouter/{annonce} AVANT /{ligne} pour éviter conflit
    Route::post('/ajouter/{annonce}', [PanierController::class, 'ajouter'])->name('ajouter');

    // Routes dynamiques /{ligne} — APRÈS les statiques
    Route::patch('/{ligne}',  [PanierController::class, 'mettreAJour'])->name('mettre-a-jour');
    Route::delete('/{ligne}', [PanierController::class, 'supprimer'])->name('supprimer');
});

// ══════════════════════════════════════════════════════════════════════════════
// COMMANDES
// ⚠️ Routes statiques (payer, success, cancel, mes-commandes, mes-ventes)
//    AVANT la route dynamique /{commande}
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->prefix('commandes')->name('commandes.')->group(function () {
    // Statiques — AVANT /{commande}
    Route::post('/payer',         [CommandeController::class, 'creerDepuisPanier'])->name('payer');
    Route::get('/success',        [CommandeController::class, 'success'])->name('success');
    Route::get('/cancel',         [CommandeController::class, 'cancel'])->name('cancel');
    Route::get('/mes-commandes',  [CommandeController::class, 'mesCommandes'])->name('mes-commandes');
    Route::get('/mes-ventes',     [CommandeController::class, 'mesVentes'])->name('mes-ventes');

    // Dynamiques /{commande} — APRÈS les statiques
    Route::get('/{commande}',                          [CommandeController::class, 'show'])->name('show');
    Route::patch('/{commande}/annuler',                [CommandeController::class, 'annuler'])->name('annuler');
    Route::patch('/{commande}/confirmer-reception',    [CommandeController::class, 'confirmerReception'])->name('confirmer-reception');
    Route::patch('/{commande}/statut',                 [CommandeController::class, 'changerStatut'])->name('statut');
    Route::post('/{commande}/rembourser',              [CommandeController::class, 'rembourser'])->name('rembourser');
});

// ══════════════════════════════════════════════════════════════════════════════
// MESSAGES
// ⚠️ Routes statiques (index, envoyer, non-lus, demarrer, supprimer-message)
//    AVANT les routes dynamiques /{conversation} et /{annonce}/{user}
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->prefix('messages')->name('messages.')->group(function () {
    // Statiques — AVANT les dynamiques
    Route::get('/',                        [MessageController::class, 'index'])->name('index');
    Route::post('/envoyer',                [MessageController::class, 'envoyer'])->name('envoyer');
    Route::get('/non-lus',                 [MessageController::class, 'nonLus'])->name('non-lus');
    Route::get('/demarrer/{annonce}',      [MessageController::class, 'demarrer'])->name('demarrer');
    Route::delete('/message/{message}',    [MessageController::class, 'supprimerMessage'])->name('supprimer-message');

    // Dynamiques — APRÈS les statiques
    Route::get('/{conversation}/polling',  [MessageController::class, 'polling'])->name('polling');
    Route::patch('/{conversation}/archiver', [MessageController::class, 'archiver'])->name('archiver');
    Route::get('/{annonce}/{user}',        [MessageController::class, 'show'])->name('show');
});

// ── STRIPE ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('stripe')->name('stripe.')->group(function () {
    Route::get('/dashboard', [StripeController::class, 'dashboard'])->name('dashboard');
});

// Webhook Stripe — sans CSRF
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Remboursement admin — route séparée pour garder le bon nom
Route::middleware(['auth', 'verified'])
    ->post('/commandes/{commande}/rembourser', [StripeController::class, 'rembourser'])
    ->name('commandes.rembourser');
// ── PROFIL ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profil',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil',  [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profil/iban', [ProfileController::class, 'updateIban'])->name('profile.iban');
});

Route::get('/qui-sommes-nous',   [PagesController::class, 'quiSommesNous'])->name('pages.qui-sommes-nous');
Route::get('/cgu',               [PagesController::class, 'cgu'])->name('pages.cgu');
Route::get('/confidentialite',   [PagesController::class, 'confidentialite'])->name('pages.confidentialite');
Route::get('/contact',           [PagesController::class, 'contact'])->name('pages.contact');
Route::post('/contact',          [PagesController::class, 'sendContact'])->name('pages.contact.send');

Route::middleware(['auth', 'verified'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',                    [NotificationController::class, 'index'])->name('index');
    Route::get('/dernieres',           [NotificationController::class, 'dernieres'])->name('dernieres');
    Route::get('/compteur',            [NotificationController::class, 'compteur'])->name('compteur');
    Route::post('/tout-lire',          [NotificationController::class, 'toutLire'])->name('tout-lire');
    Route::get('/{id}/lire',           [NotificationController::class, 'lire'])->name('lire');
    Route::delete('/{id}',             [NotificationController::class, 'supprimer'])->name('supprimer');
});

// ── AUTH ──────────────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';