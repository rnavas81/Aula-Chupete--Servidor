<?php

use App\Http\Controllers\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Users
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
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
