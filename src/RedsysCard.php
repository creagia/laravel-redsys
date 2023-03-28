<?php

namespace Creagia\LaravelRedsys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property ?string $number
 * @property ?string $uuid
 * @property ?string $expiration_date
 * @property ?string $merchant_identifier
 * @property ?string $cof_transaction_id
 */
class RedsysCard extends Model
{
    /**
     * @return MorphTo<Model, RedsysCard>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedExpiration(): string
    {
        return mb_substr($this->expiration_date, 2, 2) . '/' . mb_substr($this->expiration_date, 0, 2);
    }
}
