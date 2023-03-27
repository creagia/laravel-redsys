<?php

use Creagia\LaravelRedsys\Tests\Models\TestModel;
use Creagia\Redsys\Enums\ConsumerLanguage;
use Creagia\Redsys\Enums\PayMethod;

dataset('payment', [
    [
        fn () => $this->testModel = TestModel::create(),
        fn () => $this->redsysRequestBuilder = $this->testModel->createRedsysRequest(PayMethod::Card, ConsumerLanguage::Auto, 'Product description'),
    ],
]);
