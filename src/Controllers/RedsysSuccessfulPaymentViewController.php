<?php

namespace Creagia\LaravelRedsys\Controllers;

use Illuminate\View\View;

use function view;

class RedsysSuccessfulPaymentViewController
{
    public function __invoke(): View
    {
        return view('redsys::successful');
    }
}
