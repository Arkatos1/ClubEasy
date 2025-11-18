<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAdmin\Admin\Auth\Database\Menu;

class AdminMenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->ensureCzechMenuName();
    }

    protected function ensureCzechMenuName()
    {
        try {
            $backMenu = Menu::where('order', 1)->first();

            if ($backMenu) {
                //
            } else {
                Menu::create([
                    'title' => 'Zpátky do aplikace',
                    'icon' => 'icon-home',
                    'uri' => 'https://sportsclub.test/administration',
                    'order' => 1
                ]);
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
    }
}
