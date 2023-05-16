<?php

namespace Creagia\LaravelRedsys;

enum RedsysRequestStatus: string
{
    case Pending = 'pending';
    case Denied = 'denied';
    case Paid = 'paid';
}
