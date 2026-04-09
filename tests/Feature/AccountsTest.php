<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Requests\Accounts\CreateAccountRequest;
use Ziming\LaravelJumio\Requests\Accounts\UpdateAccountRequest;
use Ziming\LaravelJumio\Requests\Auth\RequestAccessTokenRequest;

it('creates an account session and maps the stable response data', function () {
    Saloon::fake([
        RequestAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
        CreateAccountRequest::class => MockResponse::make(accountResponsePayload(), 201),
    ]);

    $session = app(JumioClient::class)->createAccount(makeAccountRequestData());

    expect($session->accountId)->toBe('bbf3a261-9bf9-400c-b218-cd275596d8d9')
        ->and($session->workflowExecutionId)->toBe('be493071-3ffe-4c7f-9bb9-8733a614b627')
        ->and($session->sdkToken)->toBe('sdk-session-token')
        ->and($session->firstCredential()?->apiToken)->toBe('transaction-token')
        ->and($session->firstCredential()?->partUrl('front'))->toContain('/parts/FRONT');

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof CreateAccountRequest) {
            return false;
        }

        return $response->getPendingRequest()->headers()->get('User-Agent') === 'patient-crab-tests/1.0'
            && $response->getPendingRequest()->body()?->all()['callbackUrl'] === 'https://example.test/jumio/callback';
    });
});

it('updates an account with the serialized request dto payload', function () {
    Saloon::fake([
        RequestAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
        UpdateAccountRequest::class => MockResponse::make(accountResponsePayload()),
    ]);

    app(JumioClient::class)->updateAccount(
        'bbf3a261-9bf9-400c-b218-cd275596d8d9',
        makeAccountRequestData(),
    );

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof UpdateAccountRequest) {
            return false;
        }

        $body = $response->getPendingRequest()->body()?->all();

        return $response->getPendingRequest()->getUrl() === 'https://account.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9'
            && $body['workflowDefinition']['key'] === '10547'
            && $body['workflowDefinition']['credentials'][0]['country']['values'] === ['USA'];
    });
});
