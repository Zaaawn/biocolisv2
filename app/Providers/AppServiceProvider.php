<?php

namespace App\Providers;

use App\Models\Annonce;
use App\Policies\AnnoncePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
     public function boot(): void
    {
        // Enregistre la policy
        Gate::policy(Annonce::class, AnnoncePolicy::class);
    }
}
