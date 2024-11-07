<?php

namespace Creagia\LaravelRedsys\Events;

use Creagia\LaravelRedsys\Request;
use Illuminate\Foundation\Events\Dispatchable;

class RedsysSuccessfulEvent
{
    use Dispatchable;

    /**
     * @param  array<string, string>  $notificationData
     */
    public function __construct(
        public Request $redsysPayment,
        public array $notificationData,
    ) {}
}
