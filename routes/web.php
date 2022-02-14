<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Route::controller(UserController::class)->group(function () {
    Route::get('login', 'login')->name('login');
    Route::get('register', 'register');
    Route::post('user', 'get');
    Route::post('login', 'signIn');
    Route::post('register', 'signUp');
});
