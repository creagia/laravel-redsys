<?php

namespace Creagia\LaravelRedsys\Events;

use Illuminate\Foundation\Events\Dispatchable;

class RedsysNotificationEvent
{
    use Dispatchable;

    /**
     * @param  array<string, string>  $fields
     */
    public function __construct(
        public array $fields,
    ) {}
}
