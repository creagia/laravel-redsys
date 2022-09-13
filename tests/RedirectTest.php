<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

it('can redirect to Redsys', function ($testModel, $redsysPayment) {
    withoutExceptionHandling();
    get($redsysPayment->getRedirectRoute())->assertSee('realizarPago');
})->with('payment');

it('can redirect to local Redsys gateway', function ($testModel, $redsysPayment) {
    withoutExceptionHandling();

    Config::set('redsys.environment', 'local');

    get($redsysPayment->getRedirectRoute())->assertSee(action([RedsysLocalGatewayController::class, 'index']));
})->with('payment');
