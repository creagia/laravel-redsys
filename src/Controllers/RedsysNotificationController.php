<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\LaravelRedsys\Events\RedsysNotificationEvent;
use Creagia\LaravelRedsys\Events\RedsysSuccessfulEvent;
use Creagia\LaravelRedsys\Events\RedsysUnsuccessfulEvent;
use Creagia\LaravelRedsys\Exceptions\RedsysPaymentNotFound;
use Creagia\LaravelRedsys\RedsysNotificationLog;
use Creagia\LaravelRedsys\RedsysPayment;
use Creagia\LaravelRedsys\RedsysPaymentStatus;
use Creagia\Redsys\Exceptions\DeniedRedsysPaymentNotification;
use Creagia\Redsys\RedsysNotification;
use Illuminate\Http\Request;

class RedsysNotificationController
{
    public function __invoke(Request $request, CreateRedsysClient $createRedsysClient): void
    {
        $inputs = $request->all();
        RedsysNotificationEvent::dispatch($inputs);

        $redsysClient = $createRedsysClient();
        $redsysNotification = new RedsysNotification($redsysClient);
        $redsysNotification->setParametersFromResponse($inputs);

        $redsysNotificationLog = RedsysNotificationLog::create([
            'merchant_parameters' => $redsysNotification->merchantParametersArray,
        ]);

        $redsysPayment = RedsysPayment::where('order_number', $redsysNotification->parameters->order)->first();

        if (! $redsysPayment) {
            throw new RedsysPaymentNotFound('Redsys Payment not found from bank response');
        }

        $redsysNotificationLog->redsysPayment()->associate($redsysPayment);
        $redsysNotificationLog->save();

        $redsysPayment->response_code = $redsysNotification->parameters->responseCode ?? null;
        $redsysPayment->auth_code = (empty($authCode = trim($redsysNotification->parameters->responseAuthorisationCode))) ? null : $authCode;

        try {
            $notificationData = $redsysNotification->checkResponse();

            RedsysSuccessfulEvent::dispatch($redsysPayment, $notificationData->toArray());
            $redsysPayment->status = RedsysPaymentStatus::Paid;

            $redsysPayment->save();
            $redsysPayment->model->paidWithRedsys();
        } catch (DeniedRedsysPaymentNotification $e) {
            $errorMessage = $e->getMessage();
            RedsysUnsuccessfulEvent::dispatch($redsysPayment, $errorMessage);

            $redsysPayment->status = RedsysPaymentStatus::Denied;
            $redsysPayment->response_message = $errorMessage;
            $redsysPayment->save();
        }
    }
}
