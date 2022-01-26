<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

test('throw exception if is accessed from production', function ($testModel, $redsysPayment, $redsysRequest) {
    withoutExceptionHandling();

    Config::set('redsys.environment', 'local');
    App::detectEnvironment(fn () => 'production');

    post(action([RedsysLocalGatewayController::class, 'index']), $redsysRequest->getRequestFieldsArray());
})->with('payment')->throws(\Exception::class, 'Update your .env file');

it('can load local gateway', function ($testModel, $redsysPayment, $redsysRequest) {
    Config::set('redsys.environment', 'local');
    App::detectEnvironment(fn () => 'local');

    post(action([RedsysLocalGatewayController::class, 'index']), $redsysRequest->getRequestFieldsArray())->assertStatus(200);
})->with('payment');
