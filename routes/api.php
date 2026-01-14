<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterviewController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API is working']);
});

Route::post('/interview', [InterviewController::class, 'store']);
