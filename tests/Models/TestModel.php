<?php

namespace Creagia\LaravelRedsys\Tests\Models;

use Creagia\LaravelRedsys\CanCreateRedsysPayments;
use Creagia\LaravelRedsys\RedsysPayable;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model implements RedsysPayable
{
    use CanCreateRedsysPayments;

    protected $guarded = [];

    public function getTotalAmount(): float
    {
        return 123.45;
    }

    public function paidWithRedsys(): void
    {
        $this->status = 'paid';
        $this->save();
    }
}
