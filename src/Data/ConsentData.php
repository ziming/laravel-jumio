<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use DateTimeInterface;
use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class ConsentData extends Data
{
    use NormalizesPayload;

    public function __construct(
        public string $obtained,
        public DateTimeInterface|string|null $obtainedAt = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
