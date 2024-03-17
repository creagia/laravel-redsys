<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController;
use Creagia\LaravelRedsys\RequestBuilder;
use Creagia\LaravelRedsys\Tests\Models\TestModel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

test('throw exception if is accessed from production', function (TestModel $testModel, RequestBuilder $redsysRequestBuilder) {
    withoutExceptionHandling();

    Config::set('redsys.environment', 'local');
    App::detectEnvironment(fn () => 'production');

    post(action([RedsysLocalGatewayController::class, 'index']), $redsysRequestBuilder->getRequest()->getRequestFieldsArray());
})->with('payment')->throws(\Exception::class, 'Update your .env file');

it('can load local gateway', function (TestModel $testModel, RequestBuilder $redsysRequestBuilder) {
    Config::set('redsys.environment', 'local');
    App::detectEnvironment(fn () => 'local');

    post(action([RedsysLocalGatewayController::class, 'index']), $redsysRequestBuilder->getRequest()->getRequestFieldsArray())->assertStatus(200);
})->with('payment');
