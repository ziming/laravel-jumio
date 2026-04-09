<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class CredentialDefinitionData extends Data
{
    use NormalizesPayload;

    public function __construct(
        public string $category,
        public ?string $id = null,
        public ?PredefinedValueData $country = null,
        public ?PredefinedValueData $type = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
