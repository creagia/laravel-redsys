<?php

namespace Creagia\LaravelRedsys;

use Creagia\Redsys\RedsysNotification;
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
     * @return BelongsTo<RedsysPayment, RedsysNotificationLog>
     */
    public function redsysPayment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RedsysPayment::class);
    }

    public function getStatus(): RedsysPaymentStatus
    {
        if (! isset($this->merchant_parameters['Ds_Response'])) {
            return RedsysPaymentStatus::Pending;
        }

        return RedsysNotification::isAuthorisedCode($this->merchant_parameters['Ds_Response'])
            ? RedsysPaymentStatus::Paid
            : RedsysPaymentStatus::Denied;
    }
}
