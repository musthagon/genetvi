<?php

use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Rutas publicas para usuarios sin logearse
|--------------------------------------------------------------------------
|
| Las rutas publicas son para autenticación o para realizar la evaluación
|
*/
// Authentication Routes...
Route::get('/',         'Auth\LoginController@showLoginForm')->name('login');
Route::get('login',     'Auth\LoginController@showLoginForm')->name('login');
Route::post('login',    'Auth\LoginController@login');
Route::post('logout',   'Auth\LoginController@logout')->name('logout');
Route::post('/logout',  'Auth\LoginController@logout')->name('logout');

//Public evaluacion
Route::get('/evaluar_curso/id/{token}', 'PublicController@evaluacion')->name('evaluacion_link');
Route::post('/evaluar_curso/id/{token}/procesar1/{invitacion}', 'PublicController@evaluacion_procesar1')->name('evaluacion_link_procesar1');
Route::put('/evaluar_curso/id/{token}/procesar2/{invitacion}', 'PublicController@evaluacion_procesar2')->name('evaluacion_link_procesar2');
Route::post('/evaluar_curso/id/{token}/procesar2/{invitacion}', 'PublicController@evaluacion_procesar2')->name('evaluacion_link_procesar2');

/*
|--------------------------------------------------------------------------
| Rutas para usuarios autenticados
|--------------------------------------------------------------------------
|
| Generalmente los profesores son los que tendran acceso a ver la mayoría de los contenidos
|
*/

// User Routes
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/mis_cursos', 'HomeController@cursos')->name('mis_cursos');

//Cursos dashboards
Route::get('/mis_cursos/visualizar_{id}', 'HomeController@visualizar_resultados_curso')->name('curso');

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
 
    //User
    Route::post('users/agregar_usuario_cvucv', 'Voyager\VoyagerUserController@agregar_usuario_cvucv')->name('agregar_usuario_cvucv');


    //gestion
    Route::get('instrumentos/{id}/constructor', 'Voyager\InstrumentoController@constructor')->name('instrumentos.constructor');
    Route::get('gestion', 'AdminController@gestion')->name('gestion.evaluaciones');
    Route::get('gestion/{id}', 'AdminController@gestion')->name('gestion.evaluaciones2');
    Route::get('gestion/{id}/sincronizar', 'AdminController@gestion_sincronizar')->name('gestion.sincronizar');
    Route::get('gestion/{id}/sincronizar_categorias', 'AdminController@gestion_sincronizar_categorias')->name('gestion.sincronizar_categorias');
    Route::get('gestion/{id}/gestionar_evaluacion', 'AdminController@gestionar_evaluacion_categoria')->name('gestion.evaluacion_categoria');
    Route::post('gestion/{id}/gestionar_evaluacion/store', 'AdminController@gestionar_evaluacion_categoria_store')->name('gestion.evaluacion_categoria_store');
    Route::put('gestion/{id}/gestionar_evaluacion/edit', 'AdminController@gestionar_evaluacion_categoria_edit')->name('gestion.evaluacion_categoria_edit');

    //Cursos charts, dashboards, estadísticas
    Route::get('gestion/{categoria_id}/curso_{curso_id}/', 'ChartsController@visualizar_resultados_curso')->name('curso.visualizar_resultados_curso');
    Route::get('gestion/{categoria_id}/curso_{curso_id}/respuesta', 'ChartsController@visualizar_resultados_curso_respuesta_publica')->name('curso.visualizar_resultados_curso.respuesta_publica');
    Route::get('gestion/curso/{id}/consultar_grafico/', 'ChartsController@consultar_grafico')->name('curso.consultar_grafico');
    
    //Cursos charts, dashboards, estadísticas AJAX
    Route::get('gestion/curso/consultar_grafico_indicadores/', 'ChartsController@consultar_grafico_indicadores')->name('curso.consultar_grafico_indicadores');
    Route::get('gestion/curso/{curso_id}/consultar_tabla_indicador/{periodo}/{instrumento}/{categoria}/{indicador}', 'ChartsController@consultar_tabla_indicador')->name('curso.consultar_tabla_indicador');
    Route::get('gestion/curso/consultar_grafico_generales/', 'ChartsController@consultar_grafico_generales')->name('curso.consultar_grafico_generales');

    //Evaluación de cursos
    Route::get('gestion/curso/{id}/iniciar_evaluacion/', 'AdminController@iniciar_evaluacion_curso')->name('curso_iniciar_evaluacion_curso');
    Route::get('gestion/curso/{id}/finalizar_evaluacion/', 'AdminController@cerrar_evaluacion_curso')->name('curso_cerrar_evaluacion_curso');
    Route::get('gestion/{categoria_id}/curso_{curso_id}/estatus_evaluacion/', 'AdminController@estatus_evaluacion_curso')->name('curso_estatus_evaluacion_curso');
    Route::post('gestion/curso/{id}/estatus_evaluacion/invitar_evaluacion', 'AdminController@invitar_evaluacion_curso')->name('curso_invitar_evaluacion_curso');

    Route::get('gestion/curso/{id}/enviar_recordatorio/{invitacion}', 'AdminController@enviar_recordatorio')->name('curso_enviar_recordatorio');
    Route::get('gestion/curso/{id}/revocar_invitacion/{invitacion}', 'AdminController@revocar_invitacion')->name('curso_revocar_invitacion');

    //Get users ajax
    Route::get('campus_users', 'AdminController@campus_users')->name('campus_users');
    Route::get('campus_users_by_ids', 'AdminController@campus_users_by_ids')->name('campus_users_by_ids');
 });

