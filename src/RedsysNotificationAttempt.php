<?php

namespace Creagia\LaravelRedsys;

use Creagia\Redsys\RedsysNotification;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property string $merchant_parameters
 */
class RedsysNotificationAttempt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'merchant_parameters' => 'json',
    ];

    public function redsysPayment()
    {
        return $this->belongsTo(RedsysPayment::class);
    }

    public function getStatus()
    {
        if (! isset($this->merchant_parameters['Ds_Response'])) {
            return RedsysPaymentStatus::Pending;
        }

        return RedsysNotification::isAuthorisedCode($this->merchant_parameters['Ds_Response'])
            ? RedsysPaymentStatus::Paid
            : RedsysPaymentStatus::Denied;
    }
}
