<?php

namespace Creagia\LaravelRedsys;

use Creagia\Redsys\RedsysResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Carbon\Carbon $created_at
 * @property array $merchant_parameters
 */
class RedsysNotificationLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'merchant_parameters' => 'json',
    ];

    /**
     * @return BelongsTo<Request, $this>
     */
    public function redsysRequest(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function getStatus(): RedsysRequestStatus
    {
        if (! isset($this->merchant_parameters['Ds_Response'])) {
            return RedsysRequestStatus::Pending;
        }

        return RedsysResponse::isAuthorisedCode($this->merchant_parameters['Ds_Response'])
            ? RedsysRequestStatus::Success
            : RedsysRequestStatus::Error;
    }
}
