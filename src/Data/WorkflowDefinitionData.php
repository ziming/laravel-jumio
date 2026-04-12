<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class WorkflowDefinitionData extends Data
{
    use NormalizesPayload;

    /**
     * @param  array<int, CredentialDefinitionData>  $credentials
     * @param  array<string, mixed>  $capabilities
     */
    public function __construct(
        public int|string $key,
        #[DataCollectionOf(CredentialDefinitionData::class)]
        public array $credentials = [],
        public array $capabilities = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
