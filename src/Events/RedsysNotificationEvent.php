<?php

namespace Creagia\LaravelRedsys\Events;

use Illuminate\Foundation\Events\Dispatchable;

class RedsysNotificationEvent
{
    use Dispatchable;

    public function __construct(
        public array $fields,
    ) {}
}
