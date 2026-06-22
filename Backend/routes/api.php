<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PageSectionController;
use App\Http\Controllers\Api\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);

    // page management
    Route::get('/pages', [PageController::class, 'index']);
    Route::post('/pages', [PageController::class, 'store']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::put('/pages/{slug}', [PageController::class, 'update']);
    Route::delete('/pages/{slug}', [PageController::class, 'destroy']);

    // template management
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/{slug}', [TemplateController::class, 'show']);

    // page section management
    Route::post('/pages/{slug}/sections', [PageSectionController::class, 'addSection']);
    Route::put('/pages/{slug}/sections/{sectionId}/fields', [PageSectionController::class, 'updateFields']);
    Route::put('/pages/{slug}/sections/reorder', [PageSectionController::class, 'reorderSections']);
    Route::delete('/pages/{slug}/sections/{sectionId}', [PageSectionController::class, 'removeSection']);
});