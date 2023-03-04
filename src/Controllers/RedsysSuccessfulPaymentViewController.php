<?php

namespace Creagia\LaravelRedsys\Controllers;

use function view;

class RedsysSuccessfulPaymentViewController
{
    public function __invoke(): \Illuminate\View\View
    {
        return view('redsys::successful');
    }
}
