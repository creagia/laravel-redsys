<?php

namespace Creagia\LaravelRedsys\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Creagia\LaravelRedsys\RedsysPayment
 */
class RedsysPayment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-redsys';
    }
}
