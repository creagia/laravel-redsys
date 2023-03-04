<?php

namespace Creagia\LaravelRedsys;

interface RedsysPayable
{
    public function getTotalAmount(): float;

    public function paidWithRedsys(): void;
}
