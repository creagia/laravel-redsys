<?php

namespace Creagia\LaravelRedsys\Controllers;

use function view;

class RedsysSuccessfulPaymentViewController
{
    public function __invoke()
    {
        return view('redsys::successful');
    }
}
