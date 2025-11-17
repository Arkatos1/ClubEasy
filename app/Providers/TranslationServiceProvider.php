<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Add our lang directory to the translation loader paths
        $this->app->extend('translation.loader', function ($loader, $app) {
            $loader->addPath(base_path('lang'));
            return $loader;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Ensure Czech locale is set
        App::setLocale('cs');
    }
}
