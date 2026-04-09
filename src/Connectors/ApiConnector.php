<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Connectors;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Ziming\LaravelJumio\Enums\JumioRegion;

final class ApiConnector extends JumioConnector
{
    public function __construct(
        JumioRegion $region,
        private readonly string $transactionToken,
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
        return $this->region->apiBaseUrl();
    }

    protected function defaultAuth(): Authenticator
    {
        return new TokenAuthenticator($this->transactionToken);
    }
}
