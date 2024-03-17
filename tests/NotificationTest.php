<?php

use Creagia\LaravelRedsys\Controllers\RedsysNotificationController;
use Creagia\LaravelRedsys\Events\RedsysNotificationEvent;
use Creagia\LaravelRedsys\Events\RedsysSuccessfulEvent;
use Creagia\LaravelRedsys\Events\RedsysUnsuccessfulEvent;
use Creagia\Redsys\Exceptions\InvalidRedsysNotification;
use Creagia\Redsys\Exceptions\InvalidRedsysResponseException;
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
})->throws(InvalidRedsysResponseException::class);

it('saves notification attempt to database', function ($testModel, $redsysRequestBuilder) {
    $redirectResponse = $redsysRequestBuilder->redirect();

    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequestBuilder->getRequest()->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse('0000'));

    $this->assertDatabaseHas((new \Creagia\LaravelRedsys\RedsysNotificationLog())->getTable(), [
        'redsys_request_id' => $redsysRequestBuilder->request->id,
    ]);
})->with('payment');

it('changes Redsys payment status to paid', function ($testModel, $redsysRequestBuilder) {
    $redirectResponse = $redsysRequestBuilder->redirect();
    $request = $redsysRequestBuilder->request;

    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequestBuilder->getRequest()->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse('0000'));

    $request->refresh();
    expect($request->status)->toBe(\Creagia\LaravelRedsys\RedsysRequestStatus::Paid->value);
})->with('payment');

it('changes Redsys payment status to denied', function ($testModel, $redsysRequestBuilder) {
    $redirectResponse = $redsysRequestBuilder->redirect();
    $request = $redsysRequestBuilder->request;

    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequestBuilder->getRequest()->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse('0184'));

    $request->refresh();
    expect($request->status)->toBe(\Creagia\LaravelRedsys\RedsysRequestStatus::Denied->value);
})->with('payment');

it('executes payable model paid method', function ($testModel, $redsysRequestBuilder) {
    $redirectResponse = $redsysRequestBuilder->redirect();
    $request = $redsysRequestBuilder->request;

    $fakeGateway = new \Creagia\Redsys\RedsysFakeGateway(
        $redsysRequestBuilder->getRequest()->getRequestFieldsArray(),
        config('redsys.tpv.key'),
    );

    post(action(RedsysNotificationController::class), $fakeGateway->getResponse('0000'));

    $request->refresh();
    expect($request->status)->toBe(\Creagia\LaravelRedsys\RedsysRequestStatus::Paid->value);
    expect($request->model->status)->toBe('paid');
})->with('payment');
