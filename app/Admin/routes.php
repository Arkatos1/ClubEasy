<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use OpenAdmin\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => ['web'], // Remove admin.access from global
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    // Login route - no admin access required
    $router->get('auth/login', 'AuthController@getLogin')->name('login');

    // All other routes require admin access
    $router->group(['middleware' => 'admin.access'], function () use ($router) {
        $router->get('/', 'HomeController@index')->name('home');
    });
});
