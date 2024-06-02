# Upgrading

## From v2 to v3

Breaking changes:
- `RedsysRequestStatus` cases have been renamed from Denied/Paid to Error/Success.
- Database table `redsys_requests` has been updated with:
  - Rename `status` enum column options to `['pending', 'error', 'success']`
  - Drop `order_number` unique foreign.
  - Change `pay_method` column to nullable.

## From v1 to v2

Version 2.x is a complete rewrite, so there isn't a step-by-step upgrade guide. We recommend you to read the updated docs
and update your code with the renamed methods and classes.

Some major changes are:

- **Currency amounts handled in cents as integer**
- **Renamed and created new database tables and columns.** Check the newest migration to update your current database.
- Model payments Trait renamed from `CanCreateRedsysPayments` to `CanCreateRedsysRequests`
- Return type for `getTotalAmount()` changed from `float` to `int`.
- Create Redsys request method renamed with new signature:
```php
// From
$redsysPayment = $yourModel->createRedsysPayment(
    'Product description',
    RedsysCurrency::EUR,
    RedsysConsumerLanguage::Auto,
);

// To
$redsysRequest = $yourModel->createRedsysRequest(
    productDescription: 'Product description',
    payMethod: PayMethod::Bizum,
);
```
- Removed the redirection route. You should just return the redirection HTML:
```php
// From
return redirect($redsysPayment->getRedirectRoute());

// To
return $redsysRequest->redirect();
```
- Updated config file with new parameters. 
- Replaced config parameter from `min_order_num` to `order_num_prefix`
