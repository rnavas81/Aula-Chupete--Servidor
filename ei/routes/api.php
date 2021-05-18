<?php

use App\Http\Controllers\Alumnos;
use App\Http\Controllers\Aulas;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\Auxiliaries;
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
        Route::post('/test', function (Request $params) {
            return response()->noContent(200);
        });
        // Recupera el rol del usuario logeado
        Route::get('/getRol', [Authentication::class, 'getRol']);

        // Recupera todos los usuarios
        Route::get('/user', [Users::class, 'getAll']);
        // Recupera los datos de un usuario
        // Route::get('/user/{id}', [Users::class, 'get']);
        // Nueva usuario
        Route::post('/user', [Users::class, 'insert']);
        // Actualiza un usuario
        Route::put('/user/{id}', [Users::class, 'update']);
        // Elimina un usuario
        Route::delete('/user/{id}', [Users::class, 'delete']);
        // Activa un usuario
        Route::put('/user/activate/{id}', [Users::class, 'activate']);
        //Recupera las aulas de un usuario
        Route::get('/user/aulas/{id}', [Users::class, 'getAulas']);
        // Recupera los alumnos de un usuario
        Route::get('/user/alumnos', [Users::class, 'getAlumnos']);
        Route::get('/user/alumnos/{id}', [Users::class, 'getAlumnos']);
        // Comprueba si existe un email esta en uso
        Route::get('/user/email/{email}',[Users::class,'verifyEmail']);
        // Recupera los padres existentes
        Route::get('user/parents',[Users::class,'getParents']);


        /** AULAS */

        // Recupera todas las aulas
        Route::get('/aula', [Aulas::class, 'getAll']);
        // Recupera los datos de un usuario
        Route::get('/aula/{id}', [Aulas::class, 'get']);
        // Nueva usuario
        Route::post('/aula', [Aulas::class, 'insert']);
        // Actualiza un usuario
        Route::put('/aula/{id}', [Aulas::class, 'update']);
        // Elimina un usuario
        Route::delete('/aula/{id}', [Aulas::class, 'delete']);
        // Recupera los alumnos de un aula
        Route::get('/aula/alumnos/{id}',[Aulas::class,'getAlumnos']);
        // Recupera los alumnos de un aula
        Route::put('/aula/default/{id}',[Aulas::class,'setDefault']);
        //Recupera las faltas de una aula en un día
        Route::get('/aula/{id}/faltas/{fecha}',[Aulas::class,'getFaltas']);
        //Recupera los datos del diario de un aula en una fecha
        Route::get('/aula/{id}/diario/{fecha}',[Aulas::class,'getDiario']);
        // Asigna datos a un diario
        Route::post('/aula/{id}/diario/{fecha}',[Aulas::class,'setDiario']);
        // Agrega un alumno a un aula
        Route::post('/aula/{idAula}/alumno/{idAlumno}',[Aulas::class,'addAlumno']);
        // Quita un alumno de un aula
        Route::delete('/aula/{idAula}/alumno/{idAlumno}',[Aulas::class,'removeAlumno']);

        // Recupera todas las aulas
        Route::get('/alumno', [Alumnos::class, 'getAll']);
        // Recupera los datos de un usuario
        Route::get('/alumno/{id}', [Alumnos::class, 'get']);
        // Nueva usuario
        Route::post('/alumno', [Alumnos::class, 'insert']);
        // Actualiza un usuario
        Route::put('/alumno/{id}', [Alumnos::class, 'update']);
        // Elimina un usuario
        Route::delete('/alumno/{id}', [Alumnos::class, 'delete']);
        //Asigna falta a un alumno
        Route::post('/alumno/falta', [Alumnos::class, 'setFalta']);
        // Recupera los datos de un usuario
        Route::get('/alumno/{id}/diario/{dario}', [Alumnos::class, 'getDiario']);
        // Asigna datos al diario de un alumno
        Route::post('/alumno/{id}/diario/{dario}', [Alumnos::class, 'setDiario']);

        /** AUXILIAR */
        Route::get('/auxiliar/ageRange',[Auxiliaries::class,'getAgeRange']);
        Route::get('/auxiliar/genders',[Auxiliaries::class,'getGenders']);

    });
});
