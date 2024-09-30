<?php

use Illuminate\Support\Facades\Route;
use Wising\PelicanApiAddon\Http\Controllers\ApiKeyController;
use Wising\PelicanApiAddon\Http\Controllers\FileController;
use Wising\PelicanApiAddon\Http\Controllers\FreeAllocationController;
use App\Http\Middleware\Api\Client\Server\ResourceBelongsToServer;
use App\Http\Middleware\Api\Client\Server\AuthenticateServerAccess;

Route::prefix('/api/application')->middleware(['api', 'throttle:api.application'])->group(function () {

    Route::group(['prefix' => '/users'], function () {
        /** Api-Keys */
        Route::get('{user}/api-keys', [ApiKeyController::class, 'index']);
        Route::post('{user}/api-keys', [ApiKeyController::class, 'store']);
        Route::delete('{user}/api-keys/{identifier}', [ApiKeyController::class, 'delete']);
    });

    Route::group(['prefix' => '/nodes/{node}/allocations'], function () {
        Route::get('/free', FreeAllocationController::class)->name('api.application.allocations.free');
    });

});

Route::middleware(['client-api', 'throttle:api.client'])->prefix('/api/client')->group(function () {

    Route::group(['prefix' => '/servers/{server}', 'middleware' => [AuthenticateServerAccess::class, ResourceBelongsToServer::class]], function () {

        Route::group(['prefix' => '/files'], function () {
            Route::post('/update', FileController::class);
        });

    });

});
