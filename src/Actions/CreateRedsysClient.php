<?php

namespace Creagia\LaravelRedsys\Actions;

use Creagia\LaravelRedsys\Exceptions\RedsysConfigError;
use Creagia\Redsys\Enums\Environment;
use Creagia\Redsys\RedsysClient;

class CreateRedsysClient
{
    public function __invoke(): RedsysClient
    {
        if (! config('redsys.environment')) {
            throw RedsysConfigError::missingOption('Environment');
        }

        if (! config('redsys.tpv.merchantCode')) {
            throw RedsysConfigError::missingOption('Merchant Code');
        }

        if (! config('redsys.tpv.key')) {
            throw RedsysConfigError::missingOption('Key');
        }

        $customBaseUrl = null;
        if (config('redsys.environment') === 'local') {
            $environment = Environment::Custom;
            $customBaseUrl = url(config('redsys.routes_prefix').'/localGateway');
        } else {
            $environment = Environment::from(config('redsys.environment'));
        }

        return new RedsysClient(
            merchantCode: config('redsys.tpv.merchantCode'),
            secretKey: config('redsys.tpv.key'),
            terminal: config('redsys.tpv.terminal'),
            environment: $environment,
            customBaseUrl: $customBaseUrl,
        );
    }
}
