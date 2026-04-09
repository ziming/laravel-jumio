<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class WorkflowCredentialData extends Data
{
    /**
     * @param  array<int, string>  $allowedChannels
     * @param  array<string, string>  $partUrls
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public ?string $label,
        public ?string $category,
        public array $allowedChannels,
        public ?string $apiToken,
        public array $partUrls,
        public ?string $workflowExecutionUrl,
        public array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $api = is_array($payload['api'] ?? null) ? $payload['api'] : [];
        $parts = is_array($api['parts'] ?? null) ? $api['parts'] : [];

        return new self(
            id: (string) ($payload['id'] ?? ''),
            label: is_string($payload['label'] ?? null) ? $payload['label'] : null,
            category: is_string($payload['category'] ?? null) ? $payload['category'] : null,
            allowedChannels: array_values(array_map('strval', $payload['allowedChannels'] ?? [])),
            apiToken: is_string($api['token'] ?? null) ? $api['token'] : null,
            partUrls: array_filter(
                array_map(static fn (mixed $value): ?string => is_string($value) ? $value : null, $parts),
                static fn (?string $value): bool => $value !== null,
            ),
            workflowExecutionUrl: is_string($api['workflowExecution'] ?? null) ? $api['workflowExecution'] : null,
            raw: $payload,
        );
    }

    public function partUrl(string $classifier): ?string
    {
        return $this->partUrls[strtolower($classifier)] ?? $this->partUrls[$classifier] ?? null;
    }
}
