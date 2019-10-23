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
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// User Routes
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/evaluar/cursos/{id_curso}/instrumento/{id_instrumento}', 'HomeController@evaluacion')->name('evaluacion');
Route::post('/home/evaluar/cursos/{id_curso}/instrumento/{id_instrumento}/procesar', 'HomeController@evaluacion_procesar')->name('evaluacion_procesar');

//Cursos dashboards
Route::get('/home/curso/{id}', 'HomeController@visualizar_curso')->name('curso');

//Public evaluacion
Route::get('/evaluar_curso/id/{token}', 'PublicController@evaluacion')->name('evaluacion_link');
Route::post('/evaluar_curso/procesar/{invitacion}', 'PublicController@evaluacion_procesar')->name('evaluacion_link_procesar');
Route::get('/evaluar_curso/estatus/satisfactoria', 'PublicController@message')->name('evaluacion_satisfactoria');
Route::get('/evaluar_curso/estatus/error', 'PublicController@message')->name('evaluacion_erronea');


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

    Route::get('gestion', 'AdminController@gestion')->name('gestion.evaluaciones');
    Route::get('gestion/{id}', 'AdminController@gestion')->name('gestion.evaluaciones2');
    Route::get('gestion/{id}/sincronizar', 'AdminController@gestion_sincronizar')->name('gestion.sincronizar');
    Route::get('gestion/{id}/sincronizar_categorias', 'AdminController@gestion_sincronizar_categorias')->name('gestion.sincronizar_categorias');

    Route::get('gestion/{id}/gestionar_evaluacion', 'AdminController@gestionar_evaluacion_categoria')->name('gestion.evaluacion_categoria');
    Route::post('gestion/{id}/gestionar_evaluacion/store', 'AdminController@gestionar_evaluacion_categoria_store')->name('gestion.evaluacion_categoria_store');
    Route::put('gestion/{id}/gestionar_evaluacion/edit', 'AdminController@gestionar_evaluacion_categoria_edit')->name('gestion.evaluacion_categoria_edit');

    //Cursos dashboards
    Route::get('gestion/curso/{id}', 'AdminController@visualizar_curso')->name('curso.visualizar');

    //Evaluación de cursos
    Route::get('gestion/curso/{id}/iniciar_evaluacion/', 'AdminController@iniciar_evaluacion_curso')->name('curso_iniciar_evaluacion_curso');
    Route::post('gestion/curso/{id}/finalizar_evaluacion/', 'AdminController@cerrar_evaluacion_curso')->name('curso_cerrar_evaluacion_curso');
    Route::get('gestion/curso/{id}/estatus_evaluacion/', 'AdminController@estatus_evaluacion_curso')->name('curso_estatus_evaluacion_curso');

    Route::get('gestion/curso/{id}/enviar_recordatorio/{invitacion}', 'AdminController@enviar_recordatorio')->name('curso_enviar_recordatorio');
    Route::get('gestion/curso/{id}/revocar_invitacion/{invitacion}', 'AdminController@revocar_invitacion')->name('curso_revocar_invitacion');

    //Get users ajax
    Route::get('campus_users', 'AdminController@campus_users')->name('campus_users');
 });

