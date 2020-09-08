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
    return $router->app->version();
});


$router->group(['prefix' => 'api' ], function() use($router) {
    //categories api 
    $router->get('show-categories/{slug1}[/{slug2?}[/{slug3?}]]', [
        'uses' => 'CategoriesController@show_categories',
        'as'=> 'show-categories'
    ]);
    
    $router->get( '/test',[
        'uses' => 'ExampleController@test'
    ]);
    $router->get('/categories_data',[
        'uses' => 'ExampleController@get_data_categories'
    ]);
    //Header API
    $router->get('/header_json/{user_id}/{header_menu_id}',[
        'uses' => 'HeaderController@header_json'
    ]);
    //Footer API
    $router->get('/footer_json/{footer_menu_id}',[
        'uses' => 'FooterController@footer_json'
    ]);
    $router->get('/page/{slug}',[
        'uses' => 'FooterController@getPage'
    ]);


}); 
