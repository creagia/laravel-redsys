<?php

namespace Creagia\LaravelRedsys\Concerns;

use Creagia\LaravelRedsys\RedsysCard;

trait InteractsWithRedsysCards
{
    public function redsysCards()
    {
        return $this->morphMany(RedsysCard::class, 'model');
    }
}
