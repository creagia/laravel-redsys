<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\RequestBuilder;
use Creagia\LaravelRedsys\Tests\Models\TestModel;

it('can return total amount', function (TestModel $testModel, RequestBuilder $requestBuilder) {
    expect($testModel->getTotalAmount())->toEqual($testModel->getTotalAmount());
})->with('payment');

it('can create a redsys payment', function (TestModel $testModel, RequestBuilder $requestBuilder) {
    expect($requestBuilder::class)->toBe(RequestBuilder::class);
})->with('payment');
