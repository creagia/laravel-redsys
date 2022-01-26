<?php

namespace Creagia\LaravelRedsys\Controllers;

use function view;

class RedsysUnsuccessfulPaymentViewController
{
    public function __invoke()
    {
        return view('redsys::unsuccessful');
    }
}
