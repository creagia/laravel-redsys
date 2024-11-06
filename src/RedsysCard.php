<?php

namespace Creagia\LaravelRedsys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property ?string $number
 * @property ?string $uuid
 * @property ?int $expiration_date
 * @property ?string $merchant_identifier
 * @property ?string $cof_transaction_id
 * @property ?int $model_id
 * @property ?string $model_type
 */
class RedsysCard extends Model
{
    /**
     * @return MorphTo<Model, $this>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedExpiration(): string
    {
        return mb_substr((string) $this->expiration_date, 2, 2).'/'.mb_substr((string) $this->expiration_date, 0, 2);
    }
}
