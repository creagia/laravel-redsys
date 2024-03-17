<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController;
use Creagia\LaravelRedsys\RequestBuilder;
use Creagia\LaravelRedsys\Tests\Models\TestModel;
use Creagia\Redsys\Enums\ConsumerLanguage;
use Creagia\Redsys\Enums\PayMethod;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\withoutExceptionHandling;

it('can redirect to Redsys', function (TestModel $testModel, RequestBuilder $redsysRequestBuilder) {
    withoutExceptionHandling();
    $redirectResponse = $redsysRequestBuilder->redirect();
    expect($redirectResponse->content())->toContain('realizarPago');
})->with('payment');

it('can redirect to local Redsys gateway', function () {
    withoutExceptionHandling();

    Config::set('redsys.environment', 'local');

    $testModel = TestModel::create();
    $redsysRequestBuilder = $testModel->createRedsysRequest(PayMethod::Card, ConsumerLanguage::Auto, 'Product description');
    $redirectResponse = $redsysRequestBuilder->redirect();
    expect($redirectResponse->content())->toContain(action([RedsysLocalGatewayController::class, 'index']));
});
