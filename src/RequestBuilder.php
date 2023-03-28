<?php

namespace Creagia\LaravelRedsys;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\LaravelRedsys\Controllers\RedsysNotificationController;
use Creagia\LaravelRedsys\Controllers\RedsysSuccessfulPaymentViewController;
use Creagia\LaravelRedsys\Controllers\RedsysUnsuccessfulPaymentViewController;
use Creagia\Redsys\Enums\CofType;
use Creagia\Redsys\Enums\PayMethod;
use Creagia\Redsys\RedsysRequest;
use Creagia\Redsys\Support\RequestParameters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RequestBuilder
{
    private ?RedsysRequest $redsysRequest;

    private RequestParameters $requestParameters;

    private ?Model $model = null;

    private ?Model $cardModel = null;

    private bool $shouldSaveCard = false;

    private string $uuid;

    public function __construct(
        RequestParameters $requestParameters
    ) {
        $this->requestParameters = $requestParameters;
        $this->uuid = Str::uuid();

        if (! $this->requestParameters->order) {
            $this->requestParameters->order = (string) Request::getNextOrderNumber();
        }

        if (! $this->requestParameters->merchantUrl) {
            $this->requestParameters->merchantUrl = action(RedsysNotificationController::class);
        }

        if (! $this->requestParameters->urlOk) {
            $this->requestParameters->urlOk = config('redsys.successful_payment_route_name')
                ? route(config('redsys.successful_payment_route_name'), $this->uuid)
                : action(RedsysSuccessfulPaymentViewController::class, $this->uuid);
        }

        if (! $this->requestParameters->urlKo) {
            $this->requestParameters->urlKo = config('redsys.successful_payment_route_name')
                ? route(config('redsys.unsuccessful_payment_route_name'), $this->uuid)
                : action(RedsysUnsuccessfulPaymentViewController::class, $this->uuid);
        }

        $createClient = app(CreateRedsysClient::class);
        $this->redsysRequest = RedsysRequest::create(
            $createClient(),
            $requestParameters,
        );
    }

    public static function newRequest(RequestParameters $requestParameters): RequestBuilder
    {
        return new RequestBuilder(
            $requestParameters
        );
    }

    public function requestingCardToken(CofType $cofType): RequestBuilder
    {
        $this->redsysRequest->requestingCardToken($cofType);
        $this->shouldSaveCard = true;

        return $this;
    }

    public function storeCardOnModel(Model $cardModel): static
    {
        $this->cardModel = $cardModel;

        return $this;
    }

    public function usingCard(CofType $cofType, RedsysCard $redsysCard): RequestBuilder
    {
        $this->redsysRequest->usingCardToken(
            $cofType,
            $redsysCard->cof_transaction_id,
            $redsysCard->merchant_identifier,
        );
        $this->requestParameters->payMethods = PayMethod::Card;

        return $this;
    }

    public function usingCardToken(CofType $cofType, string $merchantIdentifier, string $cofTransactionId): RequestBuilder
    {
        $this->redsysRequest->usingCardToken(
            $cofType,
            $cofTransactionId,
            $merchantIdentifier,
        );

        return $this;
    }

    public function associateWithModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getRequest(): RedsysRequest
    {
        return $this->redsysRequest;
    }

    private function create(): void
    {
        $request = new Request();
        $request->uuid = $this->uuid;
        $request->save_card = $this->shouldSaveCard;
        $request->amount = $this->requestParameters->amountInCents;
        $request->currency = $this->requestParameters->currency;
        $request->pay_method = $this->requestParameters->payMethods;
        $request->transaction_type = $this->requestParameters->transactionType;
        $request->order_number = (int) $this->requestParameters->order;

        if ($this->model) {
            $request->model_id = $this->model->getKey();
            $request->model_type = $this->model::class;
        }

        if ($this->cardModel) {
            $request->card_request_model_id = $this->cardModel->getKey();
            $request->card_request_model_type = $this->cardModel::class;
        }

        $request->save();
    }

    public function redirect(): Response
    {
        $this->create();

        return response($this->redsysRequest->getRedirectFormHtml());
    }

    public function post(): \Psr\Http\Message\ResponseInterface
    {
        $this->create();

        return $this->redsysRequest->sendPostRequest();
    }
}
