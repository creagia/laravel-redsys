<?php

namespace Creagia\LaravelRedsys;

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
}
