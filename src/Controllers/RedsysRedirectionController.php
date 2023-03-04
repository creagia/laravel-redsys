<?php

namespace Creagia\LaravelRedsys\Controllers;

use Creagia\LaravelRedsys\RedsysPayment;

class RedsysRedirectionController
{
    public function __invoke(string $uuid): \Illuminate\Http\Response
    {
        /** @var RedsysPayment $redsysPayment */
        $redsysPayment = RedsysPayment::where('uuid', $uuid)->firstOrFail();
        $redsysRequest = $redsysPayment->getRedsysRequest();

        return response($redsysRequest->getFormHtml());
    }
}
