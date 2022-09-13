<?php

use Creagia\LaravelRedsys\Controllers\RedsysNotificationController;
use Creagia\LaravelRedsys\Events\RedsysNotificationEvent;
use Creagia\LaravelRedsys\Events\RedsysSuccessfulEvent;
use Creagia\LaravelRedsys\Events\RedsysUnsuccessfulEvent;
use Creagia\Redsys\Exceptions\InvalidRedsysNotification;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;

test('notification controller fires event on bank notification', function () {
    Event::fake();
    post(action(RedsysNotificationController::class), ['Ds_MerchantParameters' => '']);

    Event::assertDispatched(RedsysNotificationEvent::class);
    Event::assertNotDispatched(RedsysSuccessfulEvent::class);
    Event::assertNotDispatched(RedsysUnsuccessfulEvent::class);
});

it('throws exception if bank response is invalid', function () {
    withoutExceptionHandling();
    post(action(RedsysNotificationController::class), ['Ds_MerchantParameters' => '']);
})->throws(InvalidRedsysNotification::class);

it('saves notification attempt to database', function ($testModel, $redsysPayment, $redsysRequest) {
    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequest->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse("0000"));

    $this->assertDatabaseHas((new \Creagia\LaravelRedsys\RedsysNotificationAttempt())->getTable(), [
        'redsys_payment_id' => $redsysPayment->id,
    ]);
})->with('payment');

it('changes Redsys payment status to paid', function ($testModel, $redsysPayment, $redsysRequest) {
    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequest->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse("0000"));

    $redsysPayment->refresh();
    expect($redsysPayment->status)->toBe(\Creagia\LaravelRedsys\RedsysPaymentStatus::Paid->value);
})->with('payment');

it('changes Redsys payment status to denied', function ($testModel, $redsysPayment, $redsysRequest) {
    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequest->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse("0184"));

    $redsysPayment->refresh();
    expect($redsysPayment->status)->toBe(\Creagia\LaravelRedsys\RedsysPaymentStatus::Denied->value);
})->with('payment');

it('executes payable model paid method', function ($testModel, $redsysPayment, $redsysRequest) {
    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequest->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse("0000"));

    $redsysPayment->refresh();
    expect($redsysPayment->status)->toBe(\Creagia\LaravelRedsys\RedsysPaymentStatus::Paid->value);
    expect($redsysPayment->model->status)->toBe('paid');
})->with('payment');
