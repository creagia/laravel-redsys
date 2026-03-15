<?php

use Creagia\LaravelRedsys\RequestBuilder;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\Redsys\Support\RequestParameters;

use function Pest\Laravel\withoutExceptionHandling;

it('can redirect to Redsys with standalone payment', function () {
    withoutExceptionHandling();
    $redsysRequest = RequestBuilder::newRequest(
        new RequestParameters(
            transactionType: TransactionType::Autorizacion,
            productDescription: 'Description',
            amountInCents: 12312,
            currency: Currency::EUR,
            payMethods: PayMethod::Card,
        )
    );
    expect($redsysRequest->redirect()->content())->toContain('realizarPago');
});
