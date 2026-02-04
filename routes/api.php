<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(\App\Http\Controllers\NodeController::class)->group(function () {
    Route::get('/nodes/parents', 'indexParents');
    Route::get('/nodes/{id}/children', 'indexChildren');
    Route::post('/nodes', 'store');
    Route::delete('/nodes/{id}', 'destroy');
});
