<?php

use App\Http\Controllers\Alumnos;
use App\Http\Controllers\Aulas;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\Auxiliaries;
use App\Http\Controllers\Menus;
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
Route::post('/message',[Users::class,'addMessage']);

// TODO:quitar de aqui

// TODO:quitar hasta aqui

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
        Route::put('/user', [Users::class, 'update']);
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
        // Recupera los hijos del usuario activo
        Route::get('user/childs',[Users::class,'getChilds']);


        /** AULAS */
        // // Recupera todas las aulas
        Route::get('/aula', [Aulas::class, 'getAll']);
        // Recupera los datos de un aula
        Route::get('/aula/{id}', [Aulas::class, 'get']);
        // Nueva aula
        Route::post('/aula', [Aulas::class, 'insert']);
        // Actualiza un aula
        Route::put('/aula/{id}', [Aulas::class, 'update']);
        // Elimina un aula
        Route::delete('/aula/{id}', [Aulas::class, 'delete']);
        // Recupera los alumnos de un aula
        Route::get('/aula/alumnos/{id}',[Aulas::class,'getAlumnos']);
        // Asigna un aula como activa
        Route::put('/aula/default/{id}',[Aulas::class,'setDefault']);
        //Recupera las faltas de una aula en un d??a
        Route::get('/aula/{id}/faltas/{fecha}',[Aulas::class,'getFaltas']);
        //Recupera los datos del diario de un aula en una fecha
        Route::get('/aula/{id}/diario/{fecha}',[Aulas::class,'getDiario']);
        // Asigna datos a un diario
        Route::post('/aula/{id}/diario/{fecha}',[Aulas::class,'setDiario']);
        // Agrega un alumno a un aula
        Route::post('/aula/{idAula}/alumno/{idAlumno}',[Aulas::class,'addAlumno']);
        // Quita un alumno de un aula
        Route::delete('/aula/{idAula}/alumno/{idAlumno}',[Aulas::class,'removeAlumno']);
        // Recupera el dietario de un aula para un dia
        Route::get('/aula/{idAula}/dietario/{fecha}',[Aulas::class,'getDietarioDia']);
        // Recupera el dietario de un aula para la semana
        Route::get('/aula/{idAula}/dietario/semana/{fecha}',[Aulas::class,'getDietarioSemana']);
        // Recupera el dietario de un aula
        Route::post('/aula/{idAula}/dietario/comida',[Aulas::class,'setDietarioComida']);
        // Asigna los platos de un men?? a una semana del dietario del aula
        Route::post('/aula/{idAula}/dietario/menu',[Aulas::class,'setMenu']);

        // Recupera todas las aulas
        Route::get('/alumno', [Alumnos::class, 'getAll']);
        // Recupera los datos de un alumno
        Route::get('/alumno/{idAlumno}', [Alumnos::class, 'get']);
        // Nueva alumno
        Route::post('/alumno', [Alumnos::class, 'insert']);
        // Actualiza un alumno
        Route::put('/alumno/{idAlumno}', [Alumnos::class, 'update']);
        // Elimina un alumno
        Route::delete('/alumno/{idAlumno}', [Alumnos::class, 'delete']);
        //Asigna falta a un alumno
        Route::post('/alumno/falta', [Alumnos::class, 'setFalta']);
        // Recupera los datos de un alumno
        Route::get('/alumno/{idAlumno}/aula/{idAula}/diario/{fecha}', [Alumnos::class, 'getDiario']);
        // Asigna datos al diario de un alumno
        Route::post('/alumno/{idAlumno}/diario/{diario}', [Alumnos::class, 'setDiario']);
        // Recupera las aulas de un alumno
        Route::get('/alumno/{idAlumno}/aulas', [Alumnos::class, 'getAulas']);

        /** MENUS */
        // Recupera los men??s
        Route::get('/menu', [Menus::class, 'get']);
        // Recupera los men??s
        Route::get('/menu/{idMenu}', [Menus::class, 'get']);
        // Recupera los dias del men??
        Route::get('/menu/{idMenu}/dias', [Menus::class, 'getDias']);
        // Crea un nuevo menu
        Route::post('/menu', [Menus::class, 'insert']);
        Route::put('/menu/{idMenu}', [Menus::class, 'update']);
        Route::delete('/menu/{idMenu}', [Menus::class, 'delete']);


        /** AUXILIAR */
        Route::get('/auxiliar/ageRange',[Auxiliaries::class,'getAgeRange']);
        Route::get('/auxiliar/genders',[Auxiliaries::class,'getGenders']);
        Route::get('/auxiliar/allergens',[Auxiliaries::class,'getAllergens']);

    });
});
