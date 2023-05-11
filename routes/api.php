<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\HistoryController;

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
Route::post('register',                       [UserController::class, 'register']);
Route::post('login',                          [UserController::class, 'login']);
Route::post('logout',                         [UserController::class, 'logout']);
Route::get('me',                              [UserController::class, 'me']);
Route::delete('destroy',                      [UserController::class, 'destroy']);
Route::get('image/{id}',                      [UserController::class, 'getImage'])    ->where('id', '[0-9]+');
Route::post('image/{id}',                     [UserController::class, 'uploadImage']) ->where('id', '[0-9]+');

//曲
Route::get('song/{user_id}/all',              [SongController::class, 'getAll'])      ->where('user_id', '[0-9]+');
Route::get('song/{id}',                       [SongController::class, 'getOne'])      ->where('id', '[0-9]+');
Route::post('song/{user_id}/create',          [SongController::class, 'create'])      ->where('user_id', '[0-9]+');
Route::put('song/{user_id}/edit',             [SongController::class, 'edit'])        ->where('user_id', '[0-9]+');
Route::delete('song/destroy',                 [SongController::class, 'destroy']);

//カテゴリ
Route::get('category/{user_id}/all',          [CategoryController::class, 'getAll'])  ->where('user_id', '[0-9]+');
Route::get('category/{id}',                   [CategoryController::class, 'getOne'])  ->where('id', '[0-9]+');
Route::post('category/{user_id}/create',      [CategoryController::class, 'create'])  ->where('user_id', '[0-9]+');
Route::delete('category/{id}/destroy',        [CategoryController::class, 'destroy']) ->where('id', '[0-9]+');

//アーティスト
Route::get('artist/{user_id}/all',            [ArtistController::class, 'getAll'])    ->where('user_id', '[0-9]+');
Route::get('artist/{id}/',                    [ArtistController::class, 'getOne'])    ->where('id', '[0-9]+');
Route::post('artist/{user_id}/create',        [ArtistController::class, 'create'])    ->where('user_id', '[0-9]+');
Route::delete('artist/destroy',               [ArtistController::class, 'destroy']);

//履歴
Route::get('history/{user_id}/all',           [HistoryController::class, 'getDates']) ->where('user_id', '[0-9]+');
Route::get('history/{user_id}/songs',         [HistoryController::class, 'getSongs']) ->where('user_id', '[0-9]+');
Route::post('history/{user_id}/create',       [HistoryController::class, 'create'])   ->where('user_id', '[0-9]+');
Route::delete('history/destroy',              [HistoryController::class, 'destroy']);
