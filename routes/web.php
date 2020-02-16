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
Route::get('/api/user', 'UserController@index')->middleware(ApiAuthMiddleware::class);;
Route::delete('/api/user/{id}', 'UserController@destroy')->middleware(ApiAuthMiddleware::class);;
Route::put('/api/user/{id}', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');

// Rutas del controlador de noticias 
Route::resource('/api/noticia', 'NoticiaController');
Route::post('/api/noticia/upload', 'NoticiaController@upload');
Route::get('/api/noticia/image/{filename}', 'NoticiaController@getImage');
Route::get('/api/noticia/principal/ultima', 'NoticiaController@getNoticiaPrincipal');
Route::get('/api/noticia/todas/ultimas', 'NoticiaController@getUltimasNoticias');

// Rutas del controlador de denuncias 
Route::resource('/api/denuncia', 'DenunciaController');
Route::get('/api/denuncias/pendientes', 'DenunciaController@pendientes');
Route::get('/api/denuncias/{categoria}', 'DenunciaController@getByCategoria');
Route::post('/api/denuncia/upload', 'DenunciaController@upload');
Route::get('/api/denuncia/file/{filename}', 'DenunciaController@getFile');

// Rutas del controlador de conctactos
Route::resource('/api/contacto', 'ContactoController');
Route::post('/api/contacto/upload', 'ContactoController@upload');
Route::get('/api/contacto/file/{filename}', 'ContactoController@getFile');

// Rutas del controlador de contenido 
