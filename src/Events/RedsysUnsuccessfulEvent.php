<?php

namespace Creagia\LaravelRedsys\Events;

use Creagia\LaravelRedsys\RedsysPayment;
use Illuminate\Foundation\Events\Dispatchable;

class RedsysUnsuccessfulEvent
{
    use Dispatchable;

    public function __construct(
        public RedsysPayment $redsysPayment,
        public string $errorMessage,
    ) {
    }
}
