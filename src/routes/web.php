<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|
| Route for 'item' microservices. 
| It exposes GET, POST, PUT, PATCH, DELETE Rest APIs to the items data store. 
|
*/

//Lumen's default version route
$router->get('/', function () use ($router) {
    return $router->app->version();
});


//GET /item - get all items in a list, optionally paginated
$router->get('/items', 'ItemController@index');


//GET /item/id - get a particular item
$router->get('/item/{id}', [
			'as'=>'item.show',
			'uses' => 'ItemController@show'
			]
);

//POST /item - create and store a new item
$router->post('/item', 'ItemController@store');


//PUT /item/id - update a particular item - full update needed.
$router->put('/item/{id}', 'ItemController@replace');

//PATCH /item/id - update a particular item - only the supplied fields. 
$router->patch('/item/{id}', 'ItemController@update');


//DELETE /item/id - delete a particular item
$router->delete('/item/{id}', 'ItemController@destroy');
