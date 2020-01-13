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

Auth::routes(['register'=>false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('ct')->middleware(['auth'])->group(function () {
	Route::get('/', 'ContainerController@index');
	Route::get('index-data', 'ContainerController@indexData');
	Route::get('{id}/show', 'ContainerController@showData');
	Route::get('{id}/state/{action}', 'ContainerController@state');
	Route::get('{id}', 'ContainerController@show'); 
});