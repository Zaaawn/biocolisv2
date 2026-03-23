<?php

use App\Providers\AppServiceProvider;
use App\Providers\ServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    Intervention\Image\Laravel\ServiceProvider::class,  // ← ajoute cette ligne
];