<?php

namespace Creagia\LaravelRedsys\Contracts;

interface RedsysPayable
{
    public function getTotalAmount(): int;

    public function paidWithRedsys(): void;
}
