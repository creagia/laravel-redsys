<?php

namespace Creagia\LaravelRedsys;

use Creagia\LaravelRedsys\Observers\RedsysRequestObserver;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property \Carbon\Carbon $created_at
 * @property bool $save_card
 * @property int $order_number
 * @property ?string $response_code
 * @property ?string $response_message
 * @property ?string $auth_code
 * @property RedsysRequestStatus $status
 * @property string $uuid
 * @property Currency $currency
 * @property int $model_id
 * @property int $amount
 * @property PayMethod $pay_method
 * @property TransactionType $transaction_type
 */
class Request extends Model
{
    protected $table = 'redsys_requests';

    protected $guarded = [];

    protected $casts = [
        'save_card' => 'boolean',
        'currency' => Currency::class,
        'pay_method' => PayMethod::class,
        'transaction_type' => TransactionType::class,
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(RedsysRequestObserver::class);
    }

    /**
     * @return MorphTo<Model, Request>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, Request>
     */
    public function cardModel(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<RedsysNotificationLog>
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(RedsysNotificationLog::class);
    }

    public static function getNextOrderNumber(): int
    {
        $minNumber = date('ym').config('redsys.min_order_num').'00000';
        $last = Request::query()
            ->latest('order_number')
            ->where('order_number', '>', $minNumber)
            ->first();
        if (! $last) {
            return intval($minNumber) + 1;
        }

        return $last->order_number + 1;
    }
}