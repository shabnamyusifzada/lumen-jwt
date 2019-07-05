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



$router->group(['prefix' => 'api'], function ($router){
    $router->get('category', 'CategoryController@getCategory');
    $router->post('category', 'CategoryController@postCategory');
    $router->get('country', 'CountryController@getCountry');
    $router->post('country', 'CountryController@postCountry');
    $router->post('register', 'UserController@register');
    $router->post('login', 'AuthController@login');
    $router->group(['middleware' => 'auth:api'], function($router) {
        $router->post('forgotpassword/{id}', 'UserController@forgotPassword');
        $router->post('changepassword/{id}', 'UserController@changePassword');
        $router->get('subcategories/{id}', 'CategoryController@listCategories');
        $router->get('cities/{id}', 'CountryController@listCountries');
        $router->get('profile/{id}', 'UserController@userProfile');
        $router->post('profile/{id}', 'UserController@uploadImage');

    });
});




