<?php

namespace Creagia\LaravelRedsys;

enum RedsysRequestStatus: string
{
    case Pending = 'pending';
    case Error = 'error';
    case Success = 'success';
}
