<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'event'], function () use ($router) {
    $router->post('/', ['as' => 'create_event', 'uses' => 'EventController@create']);

    $router->get('/', ['as' => 'get_all_events', 'uses' => 'EventController@readAll']);

    $router->get("/{id:[0-9]+}", ['as' => 'get_single_event', 'uses' => 'EventController@read']);

    $router->get("/{id:[0-9]+}/codes", ['as' => 'get_single_event_with_promo_codes', "uses" => "EventController@readWithPromoCodes"]);

    $router->post("/{id:[0-9]+}/update", ["as" => "update_event", "uses" => "EventController@updateEvent"]);

    $router->get("/{id:[0-9]+}/delete", ["as" => "delete_event", "uses" => "EventController@deleteEvent"]);
});

$router->group(['prefix' => 'promo-code'], function() use($router) {
    $router->post('/', ['as' => 'create_promo_code', 'uses' => 'PromoCodeController@createPromoCode']);

    $router->get('/', ['as' => 'get_all_promo_codes', 'uses' => 'PromoCodeController@readAllPromoCodes']);

    $router->get('/active', ['as' => 'get_all_active_promo_codes', 'uses' => 'PromoCodeController@readAllActivePromoCodes']);

    $router->get('/{id:[0-9]+}/deactivate', ['as' => 'deactivate_promo_codes', 'uses' => 'PromoCodeController@deactivatePromoCode']);

    $router->get('/{id:[0-9]+}/activate', ['as' => 'activate_promo_codes', 'uses' => 'PromoCodeController@activateDeactivatedPromoCode']);

    $router->post('/{id:[0-9]+}/configure-radius', ['as' => 'configure_promo_code_radius', 'uses' => 'PromoCodeController@configurePromoCodeRadius']);

    $router->post('/use', ['as' => 'use_promo_code', 'uses' => 'PromoCodeController@usePromoCode']);
});

