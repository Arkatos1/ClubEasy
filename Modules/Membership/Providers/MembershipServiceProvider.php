<?php

namespace Modules\Membership\Providers;

use Illuminate\Support\ServiceProvider;

class MembershipServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        \Log::info('MembershipServiceProvider booting...');
        \Log::info('Views path: ' . module_path('Membership', 'resources/views'));

        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Membership', 'database/migrations'));
    }
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('Membership', 'config/config.php') => config_path('membership.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path('Membership', 'config/config.php'), 'membership'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $sourcePath = module_path('Membership', 'resources/views');

        $this->loadViewsFrom($sourcePath, 'membership');
    }
}
