<?php

namespace Creagia\LaravelRedsys;

enum RedsysPaymentStatus: string
{
    case Pending = 'pending';
    case Denied = 'denied';
    case Paid = 'paid';
}
