<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register'=>true]);



	Route::get('ct/all', 'ContainerController@all');
Route::prefix('ct')->middleware(['auth', 'throttle:60,1'])->group(function () {
	Route::get('/', 'ContainerController@index');
	Route::get('index-data', 'ContainerController@indexData');
	Route::post('{id}/update', 'ContainerController@update');
	Route::post('create', 'ContainerController@create');
	Route::get('{id}/show', 'ContainerController@showData');
	Route::get('{id}/edit', 'ContainerController@edit');
	Route::get('{id}/rebuild', 'ContainerController@rebuild');
	Route::get('{id}/state/{action}', 'ContainerController@state');
	Route::get('{id}', 'ContainerController@show'); 
});
	Route::post('ct/create', 'ContainerController@create');