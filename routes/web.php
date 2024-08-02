<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LigaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('liga',[LigaController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
