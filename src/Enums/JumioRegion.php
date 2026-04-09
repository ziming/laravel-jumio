<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Enums;

use Ziming\LaravelJumio\Exceptions\JumioException;

enum JumioRegion: string
{
    case Amer1 = 'amer-1';
    case Emea1 = 'emea-1';
    case Apac1 = 'apac-1';

    public static function fromConfig(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new JumioException(sprintf(
                'Unsupported Jumio region [%s]. Expected one of: %s.',
                $value,
                implode(', ', array_map(static fn (self $region): string => $region->value, self::cases())),
            ));
    }

    public function authBaseUrl(): string
    {
        return sprintf('https://auth.%s.jumio.ai', $this->value);
    }

    public function accountBaseUrl(): string
    {
        return sprintf('https://account.%s.jumio.ai', $this->value);
    }

    public function apiBaseUrl(): string
    {
        return sprintf('https://api.%s.jumio.ai', $this->value);
    }

    public function retrievalBaseUrl(): string
    {
        return sprintf('https://retrieval.%s.jumio.ai', $this->value);
    }
}
