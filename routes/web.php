<?php

use App\Http\Controllers\ReadCsvController;
use Illuminate\Support\Facades\Route;

Route::view(uri: '/', view: 'index')
    ->name('home');

Route::post('/', ReadCsvController::class)
    ->name('read');
