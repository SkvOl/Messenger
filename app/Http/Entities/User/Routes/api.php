<?php
use App\Http\Entities\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('signup', [UserController::class, 'signup']);

Route::post('signin', [UserController::class, 'signin']);

Route::get('user', [UserController::class, 'user'])->middleware('auth:sanctum');

Route::get('current_user', [UserController::class, 'current_user'])->middleware('auth:sanctum');

Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');