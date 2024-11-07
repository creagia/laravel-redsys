<?php

namespace Creagia\LaravelRedsys\Actions;

use Creagia\LaravelRedsys\Contracts\RedsysPayable;
use Creagia\LaravelRedsys\Events\RedsysSuccessfulEvent;
use Creagia\LaravelRedsys\Events\RedsysUnsuccessfulEvent;
use Creagia\LaravelRedsys\Exceptions\RedsysRequestNotFound;
use Creagia\LaravelRedsys\RedsysCard;
use Creagia\LaravelRedsys\RedsysNotificationLog;
use Creagia\LaravelRedsys\RedsysRequestStatus;
use Creagia\LaravelRedsys\Request;
use Creagia\Redsys\Exceptions\DeniedRedsysPaymentResponseException;
use Creagia\Redsys\RedsysResponse;
use Creagia\Redsys\Support\PostRequestError;
use Illuminate\Support\Str;

class HandleRedsysResponse
{
    public function __invoke(
        ?Request $request,
        RedsysResponse|PostRequestError $response,
    ): void {
        $redsysNotificationLog = RedsysNotificationLog::create([
            'merchant_parameters' => $response instanceof PostRequestError
                ? $response->responseParameters
                : $response->merchantParametersArray,
        ]);

        if (! $request) {
            throw new RedsysRequestNotFound('Redsys Request not found from bank response');
        }

        $redsysNotificationLog->redsysRequest()->associate($request);
        $redsysNotificationLog->save();

        if ($response instanceof PostRequestError) {
            /**
             * Error
             */
            $request->status = RedsysRequestStatus::Error;
            $request->response_message = $response->message;
            $request->response_code = $response->code;
            $request->save();

            return;
        }

        $request->auth_code = (empty($authCode = trim($response->parameters->responseAuthorisationCode))) ? null : $authCode;
        $request->response_code = $response->parameters->responseCode ?? null;
        $request->response_message = $response->parameters->responseDescription;

        try {
            $notificationData = $response->checkResponse();

            RedsysSuccessfulEvent::dispatch($request, $notificationData->toArray());
            $request->status = RedsysRequestStatus::Success;
            $request->save();

            if ($request->model instanceof RedsysPayable) {
                $request->model->paidWithRedsys();
            }

            if (
                $request->save_card
                and filled($notificationData->merchantIdentifier)
                and filled($notificationData->cofTransactionId)
            ) {
                $redsysCard = new RedsysCard;
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
        } catch (DeniedRedsysPaymentResponseException $e) {
            $errorMessage = $e->getMessage();
            RedsysUnsuccessfulEvent::dispatch($request, $errorMessage);

            $request->status = RedsysRequestStatus::Error;
            $request->response_message = $errorMessage;
            $request->save();
        }
    }
}
