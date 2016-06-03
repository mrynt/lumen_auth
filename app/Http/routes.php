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
/ Get user's group infromations
*/
$app->get('users/{id}/group',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@group'
]);

/*
/ Get user's authorizations infromations
*/
$app->get('users/{id}/authorizations',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'UserController@authorizations'
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

/*
|--------------------------------------------------------------------------
| Authorizations routes
|--------------------------------------------------------------------------
|
| Here is where you can register the authorization's routes for the application.
|
*/

/*
/ List
/ Get the authorizations list
*/
$app->get('auth/', [
  'middleware' => array(
    'authToken'
  ),
  'uses' => 'AuthorizationController@list'
]);

/*
/ Create
*/
$app->put('auth/',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'AuthorizationController@create'
]);

/*
/ Delete
*/
$app->delete('auth/{id}',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'AuthorizationController@delete'
]);

/*
/ Edit
*/
$app->post('auth/{id}/',[
    'middleware' => array(
      'authToken'
    ),
    'uses' => 'AuthorizationController@edit'
]);

/*
|--------------------------------------------------------------------------
| Group routes
|--------------------------------------------------------------------------
|
| Here is where you can register the group's routes for the application.
|
*/

/*
/ List users
/ Get list of users in this group
*/
$app->get('groups/{description}/users', [
  'middleware' => array(
    'authToken'
  ),
  'uses' => 'GroupController@listUsers'
]);

/*
/ List authorizations
/ Get list of users in this group
*/
$app->get('groups/{description}/auths', [
  'middleware' => array(
    'authToken'
  ),
  'uses' => 'GroupController@listAuths'
]);
