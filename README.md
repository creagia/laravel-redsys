<p align="center" style="margin-top: 2rem; margin-bottom: 2rem;"><img src="/art/laravel-redsys-logosvg.svg" alt="Logo Laravel Redsys" /></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/creagia/laravel-redsys.svg?style=flat-square)](https://packagist.org/packages/creagia/laravel-redsys)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/creagia/laravel-redsys/run-tests.yml?label=tests)](https://github.com/creagia/laravel-redsys/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/creagia/laravel-redsys/fix-php-code-style-issues.yml?label=code%20style)](https://github.com/creagia/laravel-redsys/actions/workflows/fix-php-code-style-issues.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/creagia/laravel-redsys.svg?style=flat-square)](https://packagist.org/packages/creagia/laravel-redsys)

## Introduction

Integrate your Laravel application with Redsys, the lead payment gateway in Spain.

This package provides:
- Redsys integration with Redirection and REST methods
- Associate Redsys request with Eloquent models
- Associate Redsys card tokens with Eloquent models
- Management for Redsys notifications (payment confirmation)
- Advanced custom Redsys requests that cover all features
- Fake gateway for local testing

You'll be able to create a payment request, redirect the user to the payment gateway and process Redsys response with 
just a few lines of code:

```php
public function createPaymentAndRedirect()
{
    $redsysRequest = $yourModel->createRedsysRequest(
        productDescription: 'Product description',
        payMethod: PayMethod::Bizum,
    );
    return $redsysRequest->redirect();
}
```

> If you are not using Laravel, check our other package 
> **[creagia/redsys-php](https://github.com/creagia/redsys-php)** for just a Redsys PHP library.

## Installation

You can install the package via composer:

```bash
composer require creagia/laravel-redsys
```

After that, you should publish and run the migrations:

```bash
php artisan vendor:publish --tag="redsys-migrations"
php artisan migrate
```

Next, you should publish the config file with:

```bash
php artisan vendor:publish --tag="redsys-config"
```

Finally, you should define, at least, the required options in your .env file:

```
REDSYS_ENVIRONMENT
REDSYS_MERCHANT_CODE
REDSYS_KEY
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
        'currency' => \Creagia\Redsys\Enums\Currency::EUR,
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
     * Use an automatic prefix for the order number with the current year and month.
     */
    'order_num_auto_prefix' => true,

    /**
     * Redsys order number should be unique. Here you can set an order number prefix if you need it.
     * This prefix must be an integer number.
     */
    'order_num_prefix' => env('REDSYS_ORDER_NUM_PREFIX', 0),
];
```

## Usage

* [Introduction](#introduction)
* [Associate requests with Eloquent models](#associate-with-models)
* [Custom Redsys requests](#custom-redsys-requests)
* [Sending requests to Redsys](#redirecting-to-redsys)
* [Credential-On-File requests](#cof-requests)
* [Local Gateway](#local-gateway)
* [Unsuccessful or abandoned payments](#unsuccessful-or-abandoned-payments)
* [Events](#events)

<a name="introduction"></a>
### Introduction

This packages integrates [redsys-php](https://github.com/creagia/redsys-php) with your Laravel application.
You can use it in two different ways:

- [Associating requests with Eloquent models](#associate-with-models)
- [Creating standalone Redsys requests](#standalone-redsys-requests)

<a name="associate-with-models"></a>
### Associate requests with Eloquent models

Add the `CanCreateRedsysRequests` trait and implement the `RedsysPayable` class to the model you would like make payable.

Typically, this model would be something like `Order` for a full ecommerce or cart system, but you can associate payments
with multiple Eloquent models if you prefer it.

You should implement your `getTotalAmount` and `paidWithRedsys` methods. The first one will return the computed total 
amount for the payment **in cents**. The second will be executed when Redsys confirms the payment was successful.

```php
use Creagia\LaravelRedsys\Concerns\CanCreateRedsysRequests;
use Creagia\LaravelRedsys\Contracts\RedsysPayable;

class YourModel extends Model implements RedsysPayable
{
    use CanCreateRedsysRequests;

    public function getTotalAmount(): int
    {
        return 199_99;
    }

    public function paidWithRedsys(): void
    {
        // Notify user, change status to paid, ...
    }
}
```

Create request:

```php
use Creagia\Redsys\Enums\PayMethod;

$redsysRequest = $yourModel->createRedsysRequest(
    productDescription: 'Product description',
    payMethod: PayMethod::Card,
);
```

<a name="custom-redsys-requests"></a>
### Custom Redsys requests

If you prefer to not associate a Redsys request to an Eloquent model, or you need to create a more complex request,
you can create a custom request easily.

This way you can totally customize the request and implement every Redsys feature available. The request input 
parameters are defined with a `RequestParameters` object that implements all the [available parameters](https://pagosonline.redsys.es/parametros-entrada-salida.html)

```php
use Creagia\LaravelRedsys\RequestBuilder;

$redsysRequestBuilder = RequestBuilder::newRequest(
    new \Creagia\Redsys\Support\RequestParameters(
        transactionType: \Creagia\Redsys\Enums\TransactionType::Autorizacion,
        productDescription: 'Description',
        amountInCents: 123_12,
        currency: Currency::EUR,
        payMethods: \Creagia\Redsys\Enums\PayMethod::Card,
    )
);
```

The `RequestBuilder` has some middle methods to help you create your requests. You can associate your custom request
with an eloquent model using the `associateWithModel()` method and interact with credit card tokens with `requestingCardToken()`
or `usingCardToken()` methods. Check the [Credential-On-File requests](#cof-requests) section to know more about it.

After creating the request you should continue on the next section to send the request to Redsys.

<a name="redirecting-to-redsys"></a>
### Sending requests to Redsys

From all the [integration methods](https://pagosonline.redsys.es/modelos-de-integracion.html) available on Redsys, you 
can implement 'Redirection' and 'REST' methods.

Once you created your Redsys request, you should send it either with redirection:

```php
use Creagia\Redsys\Enums\PayMethod;

public function redirection()
{
    $redsysRequest = $yourModel->createRedsysRequest(
        productDescription: 'Product description',
        payMethod: PayMethod::Card,
    );
    return $redsysRequest->redirect();
}
```

or send it as a POST request:

```php
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\LaravelRedsys\RequestBuilder;
use Illuminate\Database\Eloquent\Model;

public function cancellation(Model $yourModel)
{
    $redsysRequest = RequestBuilder::newRequest(new RequestParameters(
        amountInCents: $yourModel->getTotalAmount(),
        currency: Currency::EUR,
        order: '1446068581',
        transactionType: TransactionType::Anulacion,
    ))->associateWithModel($yourModel);

    return $redsysRequest->post();
}
```

> Keep in mind that the REST integration mode is not available for all features and is not always enabled by default.
Always check the Redsys documentation and your account configuration if you are not sure if you can use it or
something is not working.

#### Redsys response

Redsys notification with the request result will be automatically managed by the package. For successful payments, 
the package will run the `paidWithRedsys` method from your model.

#### Redirect users back

In the redirection method, when the client has finished, Redsys will redirect they to your website. By default, this package serves
a successful or unsuccessful route with a pretty simple view. You can override redirect routes on the config file:

```php
'successful_payment_route_name' => env('REDSYS_SUCCESSFUL_ROUTE_NAME', null),
'unsuccessful_payment_route_name' => env('REDSYS_UNSUCCESSFUL_ROUTE_NAME', null),
```

<p align="center"><img src="/art/default-redirect-view.png" alt="Successful default view"></p>

<a name="cof-requests"></a>
### Credential-On-File (token) requests

[Credential-On-File](https://pagosonline.redsys.es/funcionalidades-COF.html) requests uses authorized stored card
data to create future requests after an initial one. This is useful for a few use cases like subscriptions with
recurring payments or installments for individual payments.

While you can create [Credential-On-File](https://pagosonline.redsys.es/funcionalidades-COF.html) with a
[custom Redsys request](#standalone-redsys-requests) as defined on Redsys documentation, this packages provides
some helpers to make it easier.

Credential-On-File transactions require an initial request where you ask for a card token. After that, you can
create new requests with the card token stored on your application.

#### Prepare your Eloquent model

Use the `InteractsWithRedsysCards` concern on the model you want to associate with Redsys card tokens. Typically
this model will be `User`, `Team` or `Subscription`, for example.

```php
use Creagia\LaravelRedsys\Concerns\InteractsWithRedsysCards;

class Team extends Model
{
    use InteractsWithRedsysCards;

    ...
}
```

#### Initial request

```php
use Creagia\Redsys\Enums\CofType;
use Creagia\Redsys\Enums\PayMethod;

/**
 * Use this example to associate the request and card easily to Eloquent models
 */
public function initialRequest()
{
    $redsysRequest = $yourProductModel->createRedsysRequest(
        productDescription: 'Product description',
        payMethod: PayMethod::Card,
    )->requestingCardToken(
        CofType::Recurring
    )->storeCardOnModel(
        $yourPayingModel // User, Team, ...
    );
    
    return $redsysRequest->redirect();
}

/**
 * Use this example for a custom request, optionally associating the request to Eloquent models
 */
public function initialCustomRequest()
{
    $redsysRequest = RequestBuilder::newRequest(new RequestParameters(
        amountInCents: 19_99,
        currency: Currency::EUR,
        transactionType: TransactionType::Autorizacion,
    ))->associateWithModel(
        $yourProductModel
    )->requestingCardToken(
        CofType::Recurring
    )->storeCardOnModel(
        $yourPayingModel // User, Team, ...
    );
    return $redsysRequest->redirect();
}
```

#### Future requests

```php
use Creagia\Redsys\Enums\Currency;
use Creagia\Redsys\Enums\TransactionType;
use Creagia\LaravelRedsys\RequestBuilder;
use Illuminate\Database\Eloquent\Model;
use Creagia\Redsys\Enums\CofType;

public function renewSubscription(Model $yourProductModel, Model $yourPayingModel)
{
    $redsysCard = $yourPayingModel->redsysCards->last();  // User, Team, ...
    
    $redsysRequest = RequestBuilder::newRequest(new RequestParameters(
        amountInCents: $yourProductModel->getTotalAmount(),
        currency: Currency::EUR,
        transactionType: TransactionType::Autorizacion,
    ))
        ->associateWithModel(
            $yourProductModel
        )->usingCard(
            CofType::Recurring, 
            $redsysCard,
        );

    return $redsysRequest->post();
}
```

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
