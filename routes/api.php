<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/users')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::get('/current', [UserController::class, 'getCurrentUser']);
        Route::patch('/current', [UserController::class, 'updateCurrentUser']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
});

Route::prefix('/contacts')->group(function () {
    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::post('/', [ContactController::class, 'createContact']);
    });
});