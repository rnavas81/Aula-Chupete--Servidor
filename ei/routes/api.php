<?php

use App\Http\Controllers\Aulas;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/index', function () {
    return view('welcome');
});
/******* ACCESO *******/
Route::post('/login', [Authentication::class, 'login']);
Route::post('/register', [Authentication::class, 'register']);
Route::post('/forget', [Authentication::class, 'forget']);
Route::get('/activate/{code}', [Authentication::class, 'activate']);

// TODO:Aplicar middleware y passport
Route::group([], function () {
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        /** USERS */
        Route::post('/logout', [Authentication::class, 'logout']);
        Route::post('/test',function (Request $params){
            return response()->noContent(200);
        });
        // Recupera el rol del usuario logeado
        Route::get('/getRol', [Authentication::class, 'getRol']);

        // Recupera todos los usuarios
        Route::get('/user',[Users::class,'getAll']);
        // Recupera los datos de un usuario
        Route::get('/user/{id}',[Users::class,'get']);
        // Nueva usuario
        Route::post('/user', [Users::class, 'insert']);
        // Actualiza un usuario
        Route::put('/user/{id}', [Users::class, 'update']);
        // Elimina un usuario
        Route::delete('/user/{id}', [Users::class, 'delete']);
        // Activa un usuario
        Route::put('/user/activate/{id}', [Users::class, 'activate']);


        /** AULAS */

        // Recupera todas las aulas
        Route::get('/aula',[Aulas::class,'getAll']);
        // Recupera los datos de un usuario
        Route::get('/aula/{id}',[Aulas::class,'get']);
        // Nueva usuario
        Route::post('/aula', [Aulas::class, 'insert']);
        // Actualiza un usuario
        Route::put('/aula/{id}', [Aulas::class, 'update']);
        // Elimina un usuario
        Route::delete('/aula/{id}', [Aulas::class, 'delete']);

    });
});
