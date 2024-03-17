<?php

namespace Creagia\LaravelRedsys\Concerns;

use Creagia\LaravelRedsys\Request;
use Creagia\LaravelRedsys\RequestBuilder;
use Creagia\Redsys\Enums\ConsumerLanguage;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\Redsys\Support\RequestParameters;

trait CanCreateRedsysRequests
{
    public function createRedsysRequest(
        PayMethod $payMethod = PayMethod::Card,
        ConsumerLanguage $language = ConsumerLanguage::Auto,
        ?string $productDescription = null,
    ): RequestBuilder {
        $currency = config('redsys.tpv.currency');

        return RequestBuilder::newRequest(new RequestParameters(
            productDescription: $productDescription,
            amountInCents: $this->getTotalAmount(),
            currency: $currency,
            payMethods: $payMethod,
            consumerLanguage: $language,
            transactionType: TransactionType::Autorizacion,
        ))->associateWithModel($this);
    }

    public function redsysRequests()
    {
        return $this->morphMany(Request::class, 'model');
    }
}
