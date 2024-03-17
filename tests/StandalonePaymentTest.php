<?php

use Creagia\Redsys\Enums\Currency;

use function Pest\Laravel\withoutExceptionHandling;

it('can redirect to Redsys with standalone payment', function () {
    withoutExceptionHandling();
    $redsysRequest = \Creagia\LaravelRedsys\RequestBuilder::newRequest(
        new \Creagia\Redsys\Support\RequestParameters(
            transactionType: \Creagia\Redsys\Enums\TransactionType::Autorizacion,
            productDescription: 'Description',
            amountInCents: 12312,
            currency: Currency::EUR,
            payMethods: \Creagia\Redsys\Enums\PayMethod::Card,
        )
    );
    expect($redsysRequest->redirect()->content())->toContain('realizarPago');
});
