<?php

namespace Creagia\LaravelRedsys;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\LaravelRedsys\Controllers\RedsysNotificationController;
use Creagia\LaravelRedsys\Controllers\RedsysRedirectionController;
use Creagia\LaravelRedsys\Controllers\RedsysSuccessfulPaymentViewController;
use Creagia\LaravelRedsys\Controllers\RedsysUnsuccessfulPaymentViewController;
use Creagia\LaravelRedsys\Observers\RedsysPaymentObserver;
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\Redsys\RedsysRequest;
use Creagia\Redsys\Support\RequestParameters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property \Carbon\Carbon $created_at
 * @property int $order_number
 * @property ?string $response_code
 * @property ?string $response_message
 * @property ?string $auth_code
 * @property RedsysPaymentStatus $status
 * @property string $uuid
 * @property int $currency
 * @property string $language
 * @property string $product_description
 * @property int $model_id
 * @property float $amount
 */
class RedsysPayment extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        self::observe(RedsysPaymentObserver::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRedirectRoute(): string
    {
        return action(RedsysRedirectionController::class, ['uuid' => $this->uuid]);
    }

    public function getRedsysRequest(): RedsysRequest
    {
        $redsysClient = app(CreateRedsysClient::class)();
        $redsysRequest = new RedsysRequest($redsysClient);

        $redsysRequest->createPaymentRequest(
            amount: $this->amount,
            orderNumber: $this->order_number,
            currency: Currency::from($this->currency),
            transactionType: TransactionType::Autorizacion,
            requestParameters: new RequestParameters(
                merchantUrl: action(RedsysNotificationController::class),
                urlOk: config('redsys.successful_payment_route_name')
                    ? route(config('redsys.successful_payment_route_name'), $this->uuid)
                    : action(RedsysSuccessfulPaymentViewController::class, $this->uuid),
                urlKo: config('redsys.successful_payment_route_name')
                    ? route(config('redsys.unsuccessful_payment_route_name'), $this->uuid)
                    : action(RedsysUnsuccessfulPaymentViewController::class, $this->uuid),
                consumerLanguage: $this->language,
                payMethods: PayMethod::Card->value,
                productDescription: $this->product_description,
            ),
        );

        return $redsysRequest;
    }

    public static function getNextOrderNumber(): int
    {
        $minNumber = date('ym') . config('redsys.min_order_num') . '00000';
        $last = RedsysPayment::query()
            ->latest('order_number')
            ->where('order_number', '>', $minNumber)
            ->first();
        if (! $last) {
            return intval($minNumber) + 1;
        }

        return $last->order_number + 1;
    }
}
