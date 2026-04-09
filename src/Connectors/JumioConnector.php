<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Connectors;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Ziming\LaravelJumio\Enums\JumioRegion;

abstract class JumioConnector extends Connector
{
    use AcceptsJson;

    public ?bool $throwOnMaxTries = false;

    public ?bool $useExponentialBackoff;

    public function __construct(
        protected readonly JumioRegion $region,
        protected readonly ?string $userAgent = null,
        protected readonly int $timeout = 30,
        protected readonly int $connectTimeout = 10,
        int $tries = 1,
        int $retryInterval = 0,
        bool $useExponentialBackoff = false,
    ) {
        $this->tries = max(1, $tries);
        $this->retryInterval = max(0, $retryInterval);
        $this->useExponentialBackoff = $useExponentialBackoff;
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return array_filter([
            'User-Agent' => $this->userAgent,
        ], static fn (?string $value): bool => $value !== null && $value !== '');
    }

    /**
     * @return array<string, int>
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
        ];
    }
}
