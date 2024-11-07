<?php

namespace Creagia\LaravelRedsys\Contracts;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface RedsysPayable
{
    public function getTotalAmount(): int;

    public function paidWithRedsys(): void;
}
