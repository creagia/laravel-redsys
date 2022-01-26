<?php

namespace Creagia\LaravelRedsys\Tests;

use Creagia\LaravelRedsys\LaravelRedsysServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Creagia\\LaravelRedsys\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelRedsysServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/migrations/create_test_models_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/create_redsys_payments_table.php.stub';
        $migration->up();
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('okroute/{redsysPayment:uuid}', function () {
            echo "ok";
        })->name('okroute');
        $router->get('koroute/{redsysPayment:uuid}', function () {
            echo "ko";
        })->name('koroute');
    }
}
