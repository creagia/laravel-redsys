<?php

namespace Creagia\LaravelRedsys\Observers;

use Creagia\LaravelRedsys\RedsysPayment;
use Illuminate\Support\Str;

class RedsysPaymentObserver
{
    public function creating(RedsysPayment $tpvOrder): void
    {
        $tpvOrder->order_number = RedsysPayment::getNextOrderNumber();
        $tpvOrder->uuid = Str::uuid();
    }
}
