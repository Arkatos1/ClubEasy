<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use OpenAdmin\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Canvas\Http\Middleware\Authenticate;

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
        $table = config('admin.extensions.config.table', 'admin_config');
        if (Schema::hasTable($table)) {
            Config::load();
        }
        View::addNamespace('membership', base_path('Modules/Membership/resources/views'));

        // Replace Canvas auth middleware with our role-based access
        $this->app->bind(Authenticate::class, function ($app) {
            return new class {
                public function handle($request, $next) {
                    $user = Auth::user();

                    // Auto-login users with roles
                    if ($user && ($user->isAdmin || $user->isContributor) && !Auth::guard('web')->check()) {
                        Auth::guard('web')->login($user);
                        return $next($request);
                    }

                    // No user or no roles - show 403
                    if (!$user || !($user->isAdmin || $user->isContributor)) {
                        abort(403, 'Access denied. Admin or contributor role required.');
                    }

                    return $next($request);
                }
            };
        });
    }
}
