<?php

use Illuminate\Routing\Router;
use OpenAdmin\Admin\Facades\Admin;

OpenAdmin\Admin\Form::forget(['editor']);

// Register Open Admin routes
Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => ['web'], // Remove admin.access from global
], function (Router $router) {

    // Login/logout routes - no admin access required
    $router->get('auth/login', 'AuthController@getLogin');
    $router->post('auth/login', 'AuthController@postLogin');
    $router->get('auth/logout', 'AuthController@getLogout');

    // All other routes require admin access
    $router->group(['middleware' => 'admin.access'], function () use ($router) {
        $router->get('/', 'HomeController@index');
        $router->get('auth/setting', 'AuthController@getSetting');
        $router->put('auth/setting', 'AuthController@putSetting');
    });
});
