<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\Exceptions\RedsysConfigError;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\RedsysRequest;
use Illuminate\Support\Facades\Config;

it('throws exception if environment config is missing', function () {
    Config::set('redsys.environment', null);

    $testModel = \Creagia\LaravelRedsys\Tests\Models\TestModel::create();
    $redsysPayment = $testModel->createRedsysPayment('Product description', Currency::EUR);
    expect($redsysPayment->getRedsysRequest())->toBe(RedsysRequest::class);
})->throws(RedsysConfigError::class, 'Environment');

it('throws exception if merchant code config is missing', function () {
    Config::set('redsys.tpv.merchantCode', null);

    $testModel = \Creagia\LaravelRedsys\Tests\Models\TestModel::create();
    $redsysPayment = $testModel->createRedsysPayment('Product description', Currency::EUR);
    expect($redsysPayment->getRedsysRequest())->toBe(RedsysRequest::class);
})->throws(RedsysConfigError::class, 'Merchant Code');


it('throws exception if key config is missing', function () {
    Config::set('redsys.tpv.key', null);

    $testModel = \Creagia\LaravelRedsys\Tests\Models\TestModel::create();
    $redsysPayment = $testModel->createRedsysPayment('Product description', Currency::EUR);
    expect($redsysPayment->getRedsysRequest())->toBe(RedsysRequest::class);
})->throws(RedsysConfigError::class, 'Key');
