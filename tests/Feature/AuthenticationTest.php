<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Requests\Accounts\CreateAccountRequest;
use Ziming\LaravelJumio\Requests\Auth\RequestAccessTokenRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowStatusRequest;

it('reuses the cached oauth token across authenticated requests', function () {
    Saloon::fake([
        RequestAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
        CreateAccountRequest::class => MockResponse::make(accountResponsePayload(), 201),
        GetWorkflowStatusRequest::class => MockResponse::make(workflowStatusPayload()),
    ]);

    $client = app(JumioClient::class);

    $session = $client->createAccount(makeAccountRequestData());
    $status = $client->getWorkflowStatus($session->accountId, $session->workflowExecutionId);

    expect($status->status)->toBe('PROCESSED');
    expect(recordedRequestCount(RequestAccessTokenRequest::class))->toBe(1);

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof CreateAccountRequest) {
            return false;
        }

        return $response->getPendingRequest()->headers()->get('Authorization') === 'Bearer oauth-access-token';
    });

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof GetWorkflowStatusRequest) {
            return false;
        }

        return $response->getPendingRequest()->headers()->get('Authorization') === 'Bearer oauth-access-token';
    });
});
