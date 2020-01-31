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

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('usuario/pruebas', 'UserController@pruebas');

// Rutas del controlador de usuarios 
Route::post('/api/registro', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');


// Rutas del controlador de noticias 
Route::resource('/api/noticia', 'NoticiaController');
Route::post('/api/noticia/upload', 'NoticiaController@upload');
Route::get('/api/noticia/image/{filename}', 'NoticiaController@getImage');
Route::get('/api/noticia/principal/ultima', 'NoticiaController@getNoticiaPrincipal');
Route::get('/api/noticia/todas/ultimas', 'NoticiaController@getUltimasNoticias');
