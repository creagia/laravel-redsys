<?php

namespace Creagia\LaravelRedsys;

use Creagia\LaravelRedsys\Actions\CreateRedsysClient;
use Creagia\Redsys\RedsysRequest;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRedsysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-redsys')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_redsys_payments_table');

        $this->app->bind('redsys-request', function () {
            $redsysClient = app(CreateRedsysClient::class)();

            return new RedsysRequest($redsysClient);
        });
    }
}
