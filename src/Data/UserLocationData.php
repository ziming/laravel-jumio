<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class UserLocationData extends Data
{
    use NormalizesPayload;

    public function __construct(
        public string $country,
        public ?string $state = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
