<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\LaravelRedsys\Actions\HandleRedsysResponse;
use Creagia\LaravelRedsys\Events\RedsysNotificationEvent;
use Creagia\LaravelRedsys\Exceptions\RedsysConfigError;
use Creagia\LaravelRedsys\Exceptions\RedsysRequestNotFound;
use Creagia\LaravelRedsys\Request;
use Creagia\Redsys\Exceptions\DeniedRedsysPaymentResponseException;
use Creagia\Redsys\Exceptions\ErrorRedsysResponseException;
use Creagia\Redsys\Exceptions\InvalidRedsysResponseException;
use Creagia\Redsys\RedsysResponse;
use Illuminate\Http\Request as HttpRequest;

class RedsysNotificationController
{
    public function __construct(
        private HandleRedsysResponse $handleRedsysResponse,
    ) {}

    /**
     * @throws DeniedRedsysPaymentResponseException
     * @throws RedsysRequestNotFound
     * @throws InvalidRedsysResponseException
     * @throws ErrorRedsysResponseException
     * @throws RedsysConfigError
     */
    public function __invoke(HttpRequest $httpRequest, CreateRedsysClient $createRedsysClient): void
    {
        $inputs = $httpRequest->all();
        RedsysNotificationEvent::dispatch($inputs);

        $redsysClient = $createRedsysClient();
        $redsysResponse = new RedsysResponse($redsysClient);
        $redsysResponse->setParametersFromResponse($inputs);

        $request = Request::where('order_number', $redsysResponse->parameters->order)->first();
        ($this->handleRedsysResponse)(
            request: $request,
            response: $redsysResponse,
        );
    }
}
