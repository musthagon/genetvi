<?php

use App\Http\Controllers\InstrumentoController;
use Illuminate\Support\Str;
use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;

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
// Authentication Routes...
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// User Routes
Route::get('/home', 'HomeController@index')->name('home');
Route::post('evaluar', 'HomeController@evaluacion')->name('evaluar');
/*
|--------------------------------------------------------------------------
| Voyager Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Voyager.
|
*/
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
 
    // Your overwrites here
    //Route::post('login', ['uses' => 'Voyager\MyAuthController@postLogin', 'as' => 'postlogin']);

    Route::get('instrumentos/{id}/constructor', 'InstrumentoController@constructor')->name('instrumentos.constructor');

    Route::get('gestion', 'HomeController@gestion')->name('gestion.evaluaciones');
    Route::get('gestion/{id}', 'HomeController@gestion')->name('gestion.evaluaciones2');

    Route::get('gestion/{id}/sincronizar_categorias', 'HomeController@gestion_sincronizar_categorias')->name('gestion.sincronizar_categorias');
 });

