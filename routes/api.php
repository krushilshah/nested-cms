<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::resource('pages', PageController::class);
Route::get('/{path}', [PageController::class, 'resolve'])->where('path', '.*');