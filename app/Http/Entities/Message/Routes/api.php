<?php
use App\Http\Entities\Message\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use App\Models\Message;

use App\Events\MessageSent;

Route::apiResource('message', MessageController::class)->middleware('auth:sanctum');

Route::patch('message/watch/{message}', [MessageController::class, 'watch'])->middleware('auth:sanctum');

Route::post('/send-message', function (\Illuminate\Http\Request $request) {
    event(new MessageSent($request->input('message')));
    
    return response()->json(['success' => true]);
});