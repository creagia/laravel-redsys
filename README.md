<p align="center" style="margin-top: 2rem; margin-bottom: 2rem;"><img src="/art/laravel-redsys-logosvg.svg" alt="Logo Laravel Redsys" /></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/creagia/laravel-redsys.svg?style=flat-square)](https://packagist.org/packages/creagia/laravel-redsys)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/creagia/laravel-redsys/run-tests?label=tests)](https://github.com/creagia/laravel-redsys/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/creagia/laravel-redsys/Check%20&%20fix%20styling?label=code%20style)](https://github.com/creagia/laravel-redsys/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/creagia/laravel-redsys.svg?style=flat-square)](https://packagist.org/packages/creagia/laravel-redsys)

## Introduction

Integrate your Laravel application with Redsys, the lead payment gateway in Spain.

You'll be able to create a payment request, redirect the user to the payment gateway and process Redsys response with 
just a few lines of code:

```php
public function createPaymentAndRedirect()
{
    $redsysPayment = $yourModel->createRedsysPayment(
        'Product description',
        RedsysCurrency::EUR,
        RedsysConsumerLanguage::Auto,
    );
    return redirect($redsysPayment->getRedirectRoute());
}
```

> If you are not using Laravel framework or prefer a different approach than associating payments to Eloquent models, 
> check our other package **[creagia/redsys-php](https://github.com/creagia/redsys-php)** for just a Redsys PHP library.

## Installation

You can install the package via composer:

```bash
composer require creagia/laravel-redsys
```

You should publish and run the migrations with:

```bash
php artisan vendor:publish --tag="redsys-migrations"
php artisan migrate
```

You should publish the config file with:

```bash
php artisan vendor:publish --tag="redsys-config"
```

This is the content of the published config file:

```php
return [
    /**
     * Used to define the service URL. Possible values 'test', 'production' or 'local'.
     *
     * It's recommended to use 'local' during your development to enable a local gateway to test your
     * application without need to expose it.
     */
    'environment' => env('REDSYS_ENVIRONMENT'),

    /**
     * Values sent to Redsys.
     */
    'tpv' => [
        'terminal' => env('REDSYS_TERMINAL', 1),
        'merchantCode' => env('REDSYS_MERCHANT_CODE'), // Default test code: 999008881
        'key' => env('REDSYS_KEY'), // Default test key: sq7HjrUOBfKmC576ILgskD5srU870gJ7
    ],

    /**
     * Prefix used by the package routes. 'redsys' by default.
     */
    'routes_prefix' => env('REDSYS_ROUTE_PREFIX', 'redsys'),

    /**
     * Route names for successful and unsuccessful confirm pages. Redsys redirects to these routes
     * after the payment is finished. By default, this package provides two neutral views.
     */
    'successful_payment_route_name' => env('REDSYS_SUCCESSFUL_ROUTE_NAME', null),
    'unsuccessful_payment_route_name' => env('REDSYS_UNSUCCESSFUL_ROUTE_NAME', null),

    /**
     * Redsys order number should be unique. You can set the starting order number here if you need it.
     */
    'min_order_num' => env('REDSYS_MIN_ORDER_NUM', 0),
];
```

## Usage

* [Preparing your model](#preparing-your-model)
* [Redirecting to Redsys](#redirecting-to-redsys)
* [Redirect to website](#redirect-to-website)
* [Local Gateway](#local-gateway)
* [Unsuccessful or abandoned payments](#unsuccessful-or-abandoned-payments)
* [Events](#events)

<a name="preparing-your-model"></a>
### Preparing your model

Add the `CanCreateRedsysPayments` trait and implement the `RedsysPayable` class to the model you would like make payable.

Typically, this model would be something like `Order` for a full ecommerce or cart system, but you can associate payments
with multiple Eloquent models if you prefer it.

You should implement your `getTotalAmount` and `paidWithRedsys` methods. The first one will return the computed total 
amount for the payment. The second will be executed when Redsys confirms the payment was successful.

```php
use Creagia\LaravelRedsys\CanCreateRedsysPayments;
use Creagia\LaravelRedsys\RedsysPayable;

class YourModel extends Model implements RedsysPayable
{
    use CanCreateRedsysPayments;

    public function getTotalAmount(): float
    {
        return 10;
    }

    public function paidWithRedsys()
    {
        // Notify user, change status to paid, ...
    }
}
```

<a name="redirecting-to-redsys"></a>
### Redirecting to Redsys

Once you configured your payable model, you can create a new payment and redirect the user to the Redsys payment page. 
Typically, that will happen in the last step of your cart or form:

```php
use Creagia\Redsys\Enums\ConsumerLanguage;
use Creagia\Redsys\Enums\Currency;

public function submit()
{
    $redsysPayment = $yourModel->createRedsysPayment(
        'Product description',
        Currency::EUR,
        ConsumerLanguage::Auto,
    );
    return redirect($redsysPayment->getRedirectRoute());
}
```

Redsys notification with the result of the payment will be automatically managed by the package. For successful payments, 
the package will execute the `paidWithRedsys` method from your model.

<a name="redirect-to-web"></a>
### Redirect to website

Once the client has finished the payment process, Redsys will redirect him to your website. By default, this package serves
a successful or unsuccessful route with a pretty simple view. You can override redirect routes on the config file:

```php
'successful_payment_route_name' => env('REDSYS_SUCCESSFUL_ROUTE_NAME', null),
'unsuccessful_payment_route_name' => env('REDSYS_UNSUCCESSFUL_ROUTE_NAME', null),
```

<p align="center"><img src="/art/default-redirect-view.png" alt="Successful default view"></p>

<a name="local-gateway"></a>
### Local Gateway

LaravelRedsys provides a practical local gateway to test your app locally without need to expose it. When your environment
config is defined to `local`, your Redsys payments will redirect to your local app instead of the test or production Redsys
url.

You'll be able to test authorised and denied payments selected the response code between the available options.

For your security, **this feature is only available if your app is set to local environment too**, apart from the package config.

<p align="center"><img src="/art/local-gateway.png" alt="Local gateway screenshot"></p>

<a name="unsuccessful-or-abandoned-payments"></a>
### Unsuccessful or abandoned payments

Unsuccessful payments won't execute any method, unlike the successful ones.

Users can abandon the payment on the Redsys side, and we wouldn't get notified on that. Because of that, you should take
care of pending/cancelled/abandoned payments on your application.

<a name="events"></a>
### Events

The package will fire some events you can listen to:

#### RedsysNotificationEvent

This event is fired when Redsys tries to notify you with a result from a payment. This event doesn't contain the `RedsysPayment` model because it's fired before processing the request.

The event has one property with the request inputs from Redsys: `$fields`.

#### RedsysSuccessfulEvent

This event is fired when a successful notification from Redsys is processed.

The event has two properties:
- `$redsysPayment`: the processed `RedsysPayment` model.
- `$notificationData`: array with the successful notification data.

#### RedsysUnsuccessfulEvent

This event is fired when an unsuccessful notification from Redsys is processed.

The event has two properties:
- `$redsysPayment`: the processed `RedsysPayment` model.
- `$errorMessage`: string with the error message from Redsys.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [David Torras](https://github.com/dtorras)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
