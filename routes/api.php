<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SongController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//ユーザ
Route::post('/register',           [UserController::class, 'register']);
Route::post('/login',              [UserController::class, 'login']);
Route::delete('/destroy/{id}',     [UserController::class, 'destroy']);
Route::get('/image/{id}',          [UserController::class, 'getImage']);
Route::post('image/{id}',          [UserController::class, 'uploadImage']);

//曲
Route::get('/song/all',            [SongController::class, 'getAll']);
Route::get('/song/{id}',           [SongController::class, 'getOne'])   ->where('id', '[0-9]+');
Route::post('/song/create',        [SongController::class, 'create']);
Route::put('song/{id}/edit',       [SongController::class, 'edit'])     ->where('id', '[0-9]+');
Route::delete('song/{id}/destroy', [SongController::class, 'destroy'])  ->where('id', '[0-9]+');

