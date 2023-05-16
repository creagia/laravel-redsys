<?php

namespace Creagia\LaravelRedsys\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \Creagia\Redsys\RedsysRequest
 */
class RedsysRequest extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redsys-request';
    }
}
