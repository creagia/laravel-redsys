<?php

namespace Creagia\LaravelRedsys\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
interface RedsysPayable
{
    public function getTotalAmount(): int;

    public function paidWithRedsys(): void;
}
