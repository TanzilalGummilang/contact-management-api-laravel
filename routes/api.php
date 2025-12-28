<?php

use App\Http\Controllers\AddressController;
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
        Route::post('/', [ContactController::class, 'create']);
        Route::get('/search', [ContactController::class, 'search']);
        Route::get('/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+');
        Route::put('/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [ContactController::class, 'delete'])->where('id', '[0-9]+');

        Route::post('/{contactId}/addresses', [AddressController::class, 'create'])
            ->where('contactId', '[0-9]+');
        Route::get('/{contactId}/addresses/{addressId}', [AddressController::class, 'get'])
            ->where('contactId', '[0-9]+')
            ->where('addressId', '[0-9]+');
        Route::get('/{contactId}/addresses', [AddressController::class, 'list'])
            ->where('contactId', '[0-9]+');
        Route::put('/{contactId}/addresses/{addressId}', [AddressController::class, 'update'])
            ->where('contactId', '[0-9]+')
            ->where('addressId', '[0-9]+');
        Route::delete('/{contactId}/addresses/{addressId}', [AddressController::class, 'delete'])
            ->where('contactId', '[0-9]+')
            ->where('addressId', '[0-9]+');
    });
});
