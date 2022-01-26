<?php

use Creagia\Redsys\Enums\Currency;

dataset('payment', [
    [
        fn () => $this->testModel = \Creagia\LaravelRedsys\Tests\Models\TestModel::create(),
        fn () => $this->redsysPayment = $this->testModel->createRedsysPayment('Product description', Currency::EUR),
        fn () => $this->redsysPayment->getRedsysRequest(),
    ],
]);
