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

$app->get('/', function () use ($app) {
  return $app->version();
});


/*
|--------------------------------------------------------------------------
| Users routes
|--------------------------------------------------------------------------
|
| Here is where you can register the user's routes for the application.
|
*/

/*
/ Login
/ Get the token
*/
$app->post('users/login', 'UserController@login');

/*
/ Register
*/
$app->post('users/register', 'UserController@register');

/*
/ Confirm
/ Confirm through token
*/
$app->get('users/confirm/{token}', 'UserController@confirm');

/*
/ Info
*/
$app->get('users/me',[
    'middleware' => 'authToken',
    'uses' => 'UserController@me'
]);
