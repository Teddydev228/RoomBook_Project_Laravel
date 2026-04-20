<?php

use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('equipment', EquipmentController::class);
});