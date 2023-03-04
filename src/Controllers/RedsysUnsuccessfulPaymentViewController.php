<?php

namespace Creagia\LaravelRedsys\Controllers;

use function view;

class RedsysUnsuccessfulPaymentViewController
{
    public function __invoke(): \Illuminate\View\View
    {
        return view('redsys::unsuccessful');
    }
}
