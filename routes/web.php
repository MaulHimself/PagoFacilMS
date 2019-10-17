<?php

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
    return 'Pago Fácil MicroService v0.1';
});


$router->post('pay', 'ApiController@pay');
$router->post('payment_complete/{token_secret}', 'ApiController@payment_complete');
$router->post('payment_cancel', 'ApiController@payment_cancel');
$router->post('payment_callback/{token_secret}', 'ApiController@payment_callback');
