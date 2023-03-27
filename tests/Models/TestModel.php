<?php

namespace Creagia\LaravelRedsys\Tests\Models;

use Creagia\LaravelRedsys\Concerns\CanCreateRedsysRequests;
use Creagia\LaravelRedsys\Contracts\RedsysPayable;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model implements RedsysPayable
{
    use CanCreateRedsysRequests;

    protected $guarded = [];

    public function getTotalAmount(): int
    {
        return 123_45;
    }

    public function paidWithRedsys(): void
    {
        $this->status = 'paid';
        $this->save();
    }
}
