<?php

use Illuminate\Support\Facades\Route;

Route::get('test/socket', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('index');
});

Route::get('chat/all', function () {
    return view('index');
});

Route::get('chat/{id}', function () {
    return view('index');
});
