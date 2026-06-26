# Zero Invoice

Zero Invoice is a PHP package for integrating third-party electronic invoice providers. The package is designed around provider-specific modules, so ECPay, NewebPay, and future vendors can be added without changing the public client API.

## Features

- Provider-based invoice factory.
- ECPay B2C electronic invoice module.
- Centralized ECPay request encryption and response decoding.
- Extensible provider registration for future vendors.
- Parameter objects for provider-specific request payloads.

## Requirements

- PHP 7.0 or higher
- Composer

## Installation

The package has not been published to Packagist yet. For open-source usage, install it through a VCS repository:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-vendor/invoice.git"
        }
    ],
    "require": {
        "zero/invoice": "dev-main"
    }
}
```

For local development:

```bash
composer install
```

## Configuration

Copy the example config file:

```bash
cp src/config.example.php src/config.php
```

Update `src/config.php` with your provider credentials. The bundled ECPay config uses ECPay's staging endpoint by default.

```php
'invoiceParameters' => [
    'MerchantID' => 2000132,
    'HashKey' => 'ejCk326UnaZWKisg',
    'HashIV' => 'q9jcZX8Ib9LM8wYk',
],
```

## Basic Usage

Create an invoice provider through the generic client entrypoint:

```php
use Zero\InvoiceClient;
use Zero\InvoiceObject;

$client = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$invoice = $client->createInvoice();
```

Full ECPay B2C examples are available in [example.php](example.php).

`createECInvoice()` is still available for backward compatibility, but new code should use `createInvoice()` so all providers share the same entrypoint.

## Provider Architecture

The package is organized into three layers:

- `InvoiceClient`: resolves a provider and invoice type to a concrete implementation.
- `Invoices/{Provider}`: contains vendor-specific logic, such as encryption, signature generation, HTTP transport, and response parsing.
- `Requests/Parameters`: contains provider-specific request DTOs.

The current built-in mapping is:

```php
InvoiceObject::INVOICE_NAME_EC + InvoiceObject::B2C => Zero\Invoices\ECPay\ECB2CInvoice
```

Future providers can be registered without modifying `InvoiceClient`:

```php
use Zero\InvoiceClient;
use Zero\InvoiceObject;
use Zero\Invoices\NewebPay\NewebPayB2CInvoice;

InvoiceClient::registerInvoice(
    InvoiceObject::INVOICE_NAME_NEWEBPAY,
    InvoiceObject::B2C,
    NewebPayB2CInvoice::class
);

$client = new InvoiceClient(InvoiceObject::INVOICE_NAME_NEWEBPAY, InvoiceObject::B2C);
$invoice = $client->createInvoice();
```

Recommended provider structure:

```text
src/
  Invoices/
    NewebPay/
      NewebPayInvoice.php
      NewebPayB2CInvoice.php
      Requests/
        Parameters/
```

This keeps each provider's API format, credentials, encryption, and response rules isolated from other vendors.

## ECPay B2C Support

ECPay B2C currently includes SDK-side implementations for:

- Invoice issue: `createIssue`
- Invoice invalidation: `createInvalid`
- Allowance issue: `createAllowance`
- Online allowance issue: `createAllowanceByCollegiate`
- Allowance invalidation: `createAllowanceInvalid`
- Online allowance invalidation: `createAllowanceInvalidByCollegiate`
- Invoice query: `getIssue`
- Invalid invoice query: `getInvalid`
- Allowance query: `getAllowanceList`
- Invalid allowance query: `getAllowanceInvalid`
- Invoice word setting query: `getInvoiceWordSetting`
- Mobile barcode validation: `isBarcode`
- Love code validation: `isLoveCode`
- Tax ID validation: `isCompanyNameByTaxId`

Current status:

- SDK request construction, encryption, sending, and response decoding are implemented.
- Real ECPay staging credentials have not yet been used to verify every API end to end.
- Query APIs currently share a generic `Query` parameter object.
- Unit tests and mocked HTTP tests still need to be expanded.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Basic syntax checks:

```bash
php -l src/InvoiceClient.php
php -l src/Invoices/ECPay/ECInvoice.php
php -l src/Invoices/ECPay/ECB2CInvoice.php
php -l example.php
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
