<?php

namespace Creagia\LaravelRedsys;

use Creagia\LaravelRedsys\Contracts\RedsysPayable;
use Creagia\LaravelRedsys\Observers\RedsysRequestObserver;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

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
 * @property ?int $model_id
 * @property ?string $model_type
 * @property ?int $card_request_model_id
 * @property ?string $card_request_model_type
 * @property int $amount
 * @property PayMethod $pay_method
 * @property TransactionType $transaction_type
 * @property-read RedsysPayable|Model|null $model
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
     * @return MorphTo<Model, $this>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function cardModel(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<RedsysNotificationLog, $this>
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(RedsysNotificationLog::class);
    }

    public static function getNextOrderNumber(): int
    {
        $prefix = config('redsys.order_num_auto_prefix', true)
            ? date('ym')
            : '';

        $minNumber = intval(Str::padRight(
            value: $prefix.config('redsys.order_num_prefix'),
            length: 12,
            pad: '0',
        ));

        $lastNumber = Request::query()
            ->latest('order_number')
            ->where('order_number', '>', $minNumber)
            ->first();

        if ($lastNumber) {
            return $lastNumber->order_number + 1;
        }

        return $minNumber + 1;
    }
}
