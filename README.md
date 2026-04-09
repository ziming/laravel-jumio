# Laravel Jumio

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/laravel-jumio.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-jumio)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/laravel-jumio/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/laravel-jumio/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/laravel-jumio.svg?style=flat-square)](https://packagist.org/packages/ziming/laravel-jumio)

`ziming/laravel-jumio` is a Laravel-first Jumio Platform client built on Saloon v4. It covers the main server-side workflow for Jumio Platform:

- OAuth client-credentials token retrieval with cache-aware refresh
- account create and update calls
- credential part uploads and workflow finalization with the transaction token returned by the account response
- retrieval status, details, steps, rules, and PDF generation

The package keeps the OAuth bearer token flow separate from the transaction-specific credential token flow because Jumio uses different auth surfaces for them.

## Installation

```bash
composer require ziming/laravel-jumio
```

Publish the config file if you want to override defaults:

```bash
php artisan vendor:publish --tag="laravel-jumio-config"
```

## Configuration

Set the Jumio credentials and region in your environment:

```env
JUMIO_REGION=amer-1
JUMIO_CLIENT_ID=your-client-id
JUMIO_CLIENT_SECRET=your-client-secret
JUMIO_USER_AGENT="my-app/1.0"
JUMIO_CALLBACK_URL=https://example.com/jumio/callback
```

Available config keys:

```php
return [
    'region' => 'amer-1', // amer-1, emea-1, apac-1
    'client_id' => env('JUMIO_CLIENT_ID'),
    'client_secret' => env('JUMIO_CLIENT_SECRET'),
    'user_agent' => env('JUMIO_USER_AGENT', 'laravel-jumio/1.0'),
    'callback_url' => env('JUMIO_CALLBACK_URL'),
    'timeout' => 30,
    'connect_timeout' => 10,
    'retry' => [
        'tries' => 1,
        'interval_ms' => 250,
        'exponential_backoff' => false,
    ],
    'cache' => [
        'store' => null,
        'prefix' => 'jumio',
        'token_refresh_buffer' => 300,
    ],
];
```

`token_refresh_buffer` is in seconds. Jumio’s OAuth access tokens are currently valid for 60 minutes, so the default buffer refreshes them 5 minutes early.

## Usage

```php
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\CredentialDefinitionData;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\PredefinedValueData;
use Ziming\LaravelJumio\Data\WorkflowDefinitionData;
use Ziming\LaravelJumio\Facades\LaravelJumio;

$session = LaravelJumio::createAccount(new AccountRequestData(
    customerInternalReference: 'customer-123',
    userReference: 'user-123',
    workflowDefinition: new WorkflowDefinitionData(
        key: '10547',
        credentials: [
            new CredentialDefinitionData(
                category: 'ID',
                country: new PredefinedValueData(['USA']),
                type: new PredefinedValueData(['PASSPORT']),
            ),
        ],
        capabilities: [
            'documentVerification' => [
                'enableExtraction' => true,
            ],
        ],
    ),
));

$credential = $session->firstCredential();

LaravelJumio::uploadCredentialPart(new CredentialPartUploadData(
    accountId: $session->accountId,
    workflowExecutionId: $session->workflowExecutionId,
    credentialId: $credential->id,
    token: $credential->apiToken,
    classifier: 'FRONT',
    file: fopen(storage_path('app/jumio/front.png'), 'rb'),
    filename: 'front.png',
));

LaravelJumio::finalizeWorkflow(new \Ziming\LaravelJumio\Data\FinalizeWorkflowData(
    accountId: $session->accountId,
    workflowExecutionId: $session->workflowExecutionId,
    token: $credential->apiToken,
));

$status = LaravelJumio::getWorkflowStatus(
    $session->accountId,
    $session->workflowExecutionId,
);
```

### Retrieval

```bash
use Ziming\LaravelJumio\Facades\LaravelJumio;

$details = LaravelJumio::getWorkflowDetails($accountId, $workflowExecutionId);
$steps = LaravelJumio::getWorkflowSteps($accountId, $workflowExecutionId);
$rules = LaravelJumio::getWorkflowRules($accountId, $workflowExecutionId);
$pdf = LaravelJumio::generateWorkflowPdf($accountId, $workflowExecutionId);
```

The status and PDF endpoints return small DTOs for the stable top-level fields. The larger retrieval endpoints return normalized arrays because Jumio’s nested retrieval payloads are large and change frequently.

### Notes

- Account and retrieval requests use the cached OAuth bearer token.
- Credential upload and finalize requests use the transaction token from the account response, not the OAuth token.
- `PREPARED_DATA` is not modeled in this initial release because the current Jumio docs are inconsistent around that request shape.

## Testing

```bash
composer test
composer analyse
```

The test suite uses `Saloon::fake()` so package behavior can be verified without hitting Jumio directly.

## Contributing

Contributions are welcome. Keep new endpoints behind typed DTOs where the response contract is stable, and prefer normalized arrays for very large retrieval payloads.

## Security Vulnerabilities

Please report security issues privately instead of opening a public issue.

## Credits

- [ziming](https://github.com/ziming)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
