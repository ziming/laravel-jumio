<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Connectors;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\BasicAuthenticator;
use Ziming\LaravelJumio\Enums\JumioRegion;

final class AuthConnector extends JumioConnector
{
    public function __construct(
        JumioRegion $region,
        private readonly string $clientId,
        private readonly string $clientSecret,
        ?string $userAgent = null,
        int $timeout = 30,
        int $connectTimeout = 10,
        int $tries = 1,
        int $retryInterval = 0,
        bool $useExponentialBackoff = false,
    ) {
        parent::__construct($region, $userAgent, $timeout, $connectTimeout, $tries, $retryInterval, $useExponentialBackoff);
    }

    public function resolveBaseUrl(): string
    {
        return $this->region->authBaseUrl();
    }

    protected function defaultAuth(): Authenticator
    {
        return new BasicAuthenticator($this->clientId, $this->clientSecret);
    }
}
