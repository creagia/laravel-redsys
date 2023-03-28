<?php

use Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController;
use Creagia\LaravelRedsys\Controllers\RedsysNotificationController;
use Creagia\LaravelRedsys\Controllers\RedsysSuccessfulPaymentViewController;
use Creagia\LaravelRedsys\Controllers\RedsysUnsuccessfulPaymentViewController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('redsys.routes_prefix'))->group(function () {
    Route::post('notification', RedsysNotificationController::class);

    Route::post('localGateway/realizarPago', [RedsysLocalGatewayController::class, 'index']);
    Route::post('localGateway/trataPeticionREST', [RedsysLocalGatewayController::class, 'rest']);
    Route::post('localGateway/post', [RedsysLocalGatewayController::class, 'post']);

    Route::get('successful-payment/{uuid}', RedsysSuccessfulPaymentViewController::class);
    Route::get('unsuccessful-payment/{uuid}', RedsysUnsuccessfulPaymentViewController::class);
});
