<?php

namespace Creagia\LaravelRedsys\Observers;

use Creagia\LaravelRedsys\Request;
use Illuminate\Support\Str;

class RedsysRequestObserver
{
    public function creating(Request $tpvOrder): void
    {
        //        $tpvOrder->order_number = Request::getNextOrderNumber();
        //        $tpvOrder->uuid = Str::uuid();
    }
}
