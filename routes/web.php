<?php

use App\Livewire\TaskBoard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders/{order}/tasks', TaskBoard::class)
    ->name('orders.tasks');
