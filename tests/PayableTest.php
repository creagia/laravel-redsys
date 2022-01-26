<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\RedsysPayment;

it('can return total amount', function ($testModel, $redsysPayment) {
    expect($testModel->getTotalAmount())->toEqual(123.45);
})->with('payment');

it('can create a redsys payment', function ($testModel, $redsysPayment) {
    expect($redsysPayment::class)->toBe(RedsysPayment::class);
})->with('payment');
