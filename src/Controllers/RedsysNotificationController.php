<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\LaravelRedsys\Events\RedsysNotificationEvent;
use Creagia\LaravelRedsys\Events\RedsysSuccessfulEvent;
use Creagia\LaravelRedsys\Events\RedsysUnsuccessfulEvent;
use Creagia\LaravelRedsys\Exceptions\RedsysRequestNotFound;
use Creagia\LaravelRedsys\RedsysCard;
use Creagia\LaravelRedsys\RedsysNotificationLog;
use Creagia\LaravelRedsys\RedsysRequestStatus;
use Creagia\LaravelRedsys\Request;
use Creagia\Redsys\Exceptions\DeniedRedsysPaymentNotification;
use Creagia\Redsys\RedsysNotification;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Str;

class RedsysNotificationController
{
    public function __invoke(HttpRequest $httpRequest, CreateRedsysClient $createRedsysClient): void
    {
        $inputs = $httpRequest->all();
        RedsysNotificationEvent::dispatch($inputs);

        $redsysClient = $createRedsysClient();
        $redsysNotification = new RedsysNotification($redsysClient);
        $redsysNotification->setParametersFromResponse($inputs);

        $redsysNotificationLog = RedsysNotificationLog::create([
            'merchant_parameters' => $redsysNotification->merchantParametersArray,
        ]);

        $request = Request::where('order_number', $redsysNotification->parameters->order)->first();

        if (! $request) {
            throw new RedsysRequestNotFound('Redsys Request not found from bank response');
        }

        $redsysNotificationLog->redsysRequest()->associate($request);
        $redsysNotificationLog->save();

        $request->response_code = $redsysNotification->parameters->responseCode ?? null;
        $request->auth_code = (empty($authCode = trim($redsysNotification->parameters->responseAuthorisationCode))) ? null : $authCode;

        try {
            $notificationData = $redsysNotification->checkResponse();

            RedsysSuccessfulEvent::dispatch($request, $notificationData->toArray());
            $request->status = RedsysRequestStatus::Paid;

            $request->save();

            if ($request->model) {
                $request->model->paidWithRedsys();
            }

            if (
                $request->save_card
                and filled($notificationData->merchantIdentifier)
                and filled($notificationData->cofTransactionId)
            ) {
                $redsysCard = new RedsysCard();
                $redsysCard->uuid = Str::uuid();
                $redsysCard->number = $notificationData->cardNumber;
                $redsysCard->expiration_date = $notificationData->cardExpiryDate;
                $redsysCard->merchant_identifier = $notificationData->merchantIdentifier;
                $redsysCard->cof_transaction_id = $notificationData->cofTransactionId;

                if ($request->card_request_model_id) {
                    $redsysCard->model_id = $request->card_request_model_id;
                    $redsysCard->model_type = $request->card_request_model_type;
                }

                $redsysCard->save();
            }
        } catch (DeniedRedsysPaymentNotification $e) {
            $errorMessage = $e->getMessage();
            RedsysUnsuccessfulEvent::dispatch($request, $errorMessage);

            $request->status = RedsysRequestStatus::Denied;
            $request->response_message = $errorMessage;
            $request->save();
        }
    }
}
