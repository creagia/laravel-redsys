<?php

namespace Creagia\LaravelRedsys;

use Creagia\Redsys\Enums\ConsumerLanguage;
use Creagia\Redsys\Enums\Currency;

trait CanCreateRedsysPayments
{
    public function createRedsysPayment(
        string $productDescription,
        Currency $currency,
        ConsumerLanguage $language = ConsumerLanguage::Auto,
    ): RedsysPayment {
        $redsysPayment = new RedsysPayment([
            'product_description' => $productDescription,
            'amount' => $this->getTotalAmount(),
            'currency' => $currency->value,
            'language' => $language->value,
        ]);

        $redsysPayment->model()->associate($this);
        $redsysPayment->save();

        return $redsysPayment;
    }
}
