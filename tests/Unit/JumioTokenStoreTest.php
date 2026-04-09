<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Auth\JumioTokenStore;
use Ziming\LaravelJumio\Requests\Auth\RequestAccessTokenRequest;

it('caches oauth tokens until they approach expiry', function () {
    Saloon::fake([
        RequestAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
    ]);

    $store = app(JumioTokenStore::class);

    $firstToken = $store->getAccessToken();
    $secondToken = $store->getAccessToken();

    expect($firstToken)->toBe('oauth-access-token')
        ->and($secondToken)->toBe('oauth-access-token')
        ->and(recordedRequestCount(RequestAccessTokenRequest::class))->toBe(1);
});
