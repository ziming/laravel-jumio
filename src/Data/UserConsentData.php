<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class UserConsentData extends Data
{
    use NormalizesPayload;

    public function __construct(
        public UserLocationData $userLocation,
        public ?ConsentData $consent = null,
        public ?string $userIp = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
