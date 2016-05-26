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
$app->post('users/login', [
    //'middleware' => 'reCAPTCHA',
    'uses' => 'UserController@login'
]);

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
/ Info user with id
*/
$app->get('users/{id}',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@info'
]);

/*
/ Edit
*/
$app->post('users/{id}/',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@edit'
]);

/*
/ Create
*/
$app->put('users/',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@create'
]);

/*
/ Remove
*/
$app->delete('users/{id}',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@delete'
]);

/*
/ Info user list
*/
$app->get('users/',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@list'
]);
