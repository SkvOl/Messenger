<?php
use App\Http\Entities\Chat\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::apiResource('chat', ChatController::class)->middleware('auth:sanctum');