<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tasks/board', function () {
    return view('tasks.board');
})->name('tasks.board');
