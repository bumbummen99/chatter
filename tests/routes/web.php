<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function() {
    return 'Hello World';
})->name('login');