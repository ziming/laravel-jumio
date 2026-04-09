<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class PredefinedValueData extends Data
{
    use NormalizesPayload;

    /**
     * @param  array<int, string>  $values
     */
    public function __construct(
        public array $values,
        public string $predefinedType = 'DEFINED',
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
