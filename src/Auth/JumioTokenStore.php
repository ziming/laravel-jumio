<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Auth;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;
use Ziming\LaravelJumio\Connectors\AuthConnector;
use Ziming\LaravelJumio\Enums\JumioRegion;
use Ziming\LaravelJumio\Exceptions\JumioException;
use Ziming\LaravelJumio\Exceptions\JumioRequestException;
use Ziming\LaravelJumio\Requests\Auth\RequestAccessTokenRequest;

final class JumioTokenStore
{
    public function __construct(
        private readonly CacheFactory $cacheFactory,
        private readonly ConfigRepository $config,
    ) {}

    public function getAccessToken(?JumioRegion $region = null): string
    {
        $region ??= $this->region();
        $cachedToken = $this->cache()->get($this->cacheKey($region));

        if (is_array($cachedToken) && $this->tokenIsFresh($cachedToken)) {
            return (string) $cachedToken['access_token'];
        }

        $payload = $this->requestFreshToken($region);

        $this->cache()->put(
            $this->cacheKey($region),
            $payload,
            CarbonImmutable::parse($payload['expires_at']),
        );

        return $payload['access_token'];
    }

    public function forget(?JumioRegion $region = null): void
    {
        $this->cache()->forget($this->cacheKey($region ?? $this->region()));
    }

    private function cache(): CacheRepository
    {
        $store = $this->config->get('jumio.cache.store');

        return is_string($store) && $store !== ''
            ? $this->cacheFactory->store($store)
            : $this->cacheFactory->store();
    }

    private function region(): JumioRegion
    {
        return JumioRegion::fromConfig((string) $this->config->get('jumio.region', 'amer-1'));
    }

    private function cacheKey(JumioRegion $region): string
    {
        $prefix = (string) $this->config->get('jumio.cache.prefix', 'jumio');

        return sprintf('%s.oauth.%s', $prefix, $region->value);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function tokenIsFresh(array $payload): bool
    {
        $accessToken = $payload['access_token'] ?? null;
        $expiresAt = $payload['expires_at'] ?? null;

        if (! is_string($accessToken) || ! is_string($expiresAt) || $accessToken === '' || $expiresAt === '') {
            return false;
        }

        $buffer = max(0, (int) $this->config->get('jumio.cache.token_refresh_buffer', 300));

        return CarbonImmutable::parse($expiresAt)->subSeconds($buffer)->isFuture();
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int, expires_at: string}
     */
    private function requestFreshToken(JumioRegion $region): array
    {
        $connector = new AuthConnector(
            region: $region,
            clientId: $this->requiredString('jumio.client_id'),
            clientSecret: $this->requiredString('jumio.client_secret'),
            userAgent: $this->optionalString('jumio.user_agent'),
            timeout: $this->intConfig('jumio.timeout', 30),
            connectTimeout: $this->intConfig('jumio.connect_timeout', 10),
            tries: $this->intConfig('jumio.retry.tries', 1),
            retryInterval: $this->intConfig('jumio.retry.interval_ms', 250),
            useExponentialBackoff: (bool) $this->config->get('jumio.retry.exponential_backoff', false),
        );

        try {
            $response = $connector->send(new RequestAccessTokenRequest);
        } catch (Throwable $throwable) {
            throw JumioException::fromThrowable($throwable, 'Unable to retrieve a Jumio OAuth access token.');
        }

        if ($response->failed()) {
            throw JumioRequestException::fromResponse($response);
        }

        /** @var array<string, mixed> $payload */
        $payload = $response->json();

        if (! is_string($payload['access_token'] ?? null)) {
            throw new JumioException('Received an invalid OAuth token payload from Jumio.');
        }

        $expiresIn = (int) ($payload['expires_in'] ?? 3600);

        return [
            'access_token' => $payload['access_token'],
            'token_type' => is_string($payload['token_type'] ?? null) ? $payload['token_type'] : 'Bearer',
            'expires_in' => $expiresIn,
            'expires_at' => CarbonImmutable::now()->addSeconds($expiresIn)->toIso8601String(),
        ];
    }

    private function requiredString(string $key): string
    {
        $value = $this->optionalString($key);

        if ($value === null) {
            throw new JumioException(sprintf('Missing Jumio configuration value [%s].', $key));
        }

        return $value;
    }

    private function optionalString(string $key): ?string
    {
        $value = $this->config->get($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    private function intConfig(string $key, int $default): int
    {
        return max(0, (int) $this->config->get($key, $default));
    }
}
