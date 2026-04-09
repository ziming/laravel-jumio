<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class WorkflowPdfData extends Data
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $workflowId,
        public string $presignedUrl,
        public array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            workflowId: (string) ($payload['workflowId'] ?? $payload['id'] ?? ''),
            presignedUrl: (string) ($payload['presignedUrl'] ?? ''),
            raw: $payload,
        );
    }
}
