<?php

namespace Creagia\LaravelRedsys\Exceptions;

class RedsysConfigError extends \Exception
{
    public static function missingOption(string $option): RedsysConfigError
    {
        return new self("Missing {$option} option from Redsys config");
    }
}
