<?php

namespace Creagia\LaravelRedsys\Events;

use Creagia\LaravelRedsys\Request;
use Illuminate\Foundation\Events\Dispatchable;

class RedsysSuccessfulEvent
{
    use Dispatchable;

    public function __construct(
        public Request $redsysPayment,
        public array $notificationData,
    ) {
    }
}
