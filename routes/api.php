<?php

use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/token', [ApiKeyController::class, 'store']);

Route::get('/positions', [PositionController::class, 'index']);

Route::get('/users', [UserController::class, 'indexAPI'])
    ->name('usersAPI');

Route::get('/users/{id}', [UserController::class, 'showAPI']);

Route::middleware('verifyAPIToken')->group(function () {
    Route::post('/users', [UserController::class, 'storeAPI']);
});

Route::any('{url?}/{sub_url?}', function(){
    return response()->json([
        'success' => false,
        'message' => 'Page not found',
    ], 404);
});
