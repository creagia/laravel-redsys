<?php

use Creagia\Redsys\RedsysClient;

use function Pest\Laravel\withoutExceptionHandling;

it('can redirect to Redsys with standalone payment', function () {
    withoutExceptionHandling();
    $redsysRequest = \Creagia\LaravelRedsys\RequestBuilder::newRequest(
        new \Creagia\Redsys\Support\RequestParameters(
            amountInCents: 12312,
            transactionType: \Creagia\Redsys\Enums\TransactionType::Autorizacion,
            currency: \Creagia\Redsys\Enums\Currency::EUR,
            payMethods: \Creagia\Redsys\Enums\PayMethod::Card,
            productDescription: 'Description',
        )
    );
    expect($redsysRequest->redirect()->content())->toContain('realizarPago');
});

it('can redirect to Redsys with an existing Redsys client', function () {
    withoutExceptionHandling();
    $redsysClient = new RedsysClient(
        merchantCode: config('redsys.tpv.merchantCode'),
        secretKey: config('redsys.tpv.key'),
        terminal: config('redsys.tpv.terminal'),
        environment: \Creagia\Redsys\Enums\Environment::Custom,
        customBaseUrl: 'customClient.org',
    );

    $redsysRequest = \Creagia\LaravelRedsys\RequestBuilder::newRequest(
        requestParameters: new \Creagia\Redsys\Support\RequestParameters(
            amountInCents: 12312,
            transactionType: \Creagia\Redsys\Enums\TransactionType::Autorizacion,
            currency: \Creagia\Redsys\Enums\Currency::EUR,
            payMethods: \Creagia\Redsys\Enums\PayMethod::Card,
            productDescription: 'Description',
        ),
        redsysClient: $redsysClient,
    );

    expect($redsysRequest->redirect()->content())->toContain('customClient.org');
});
