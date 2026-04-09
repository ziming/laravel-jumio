<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class CredentialUploadData extends Data
{
    /**
     * @param  array<string, string>  $partUrls
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $timestamp,
        public string $accountId,
        public string $workflowExecutionId,
        public ?string $credentialId,
        public ?string $partId,
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
        $account = is_array($payload['account'] ?? null) ? $payload['account'] : [];
        $workflowExecution = is_array($payload['workflowExecution'] ?? null) ? $payload['workflowExecution'] : [];
        $credential = is_array($payload['credential'] ?? null) ? $payload['credential'] : [];
        $part = is_array($payload['part'] ?? null) ? $payload['part'] : [];
        $api = is_array($payload['api'] ?? null) ? $payload['api'] : [];
        $parts = is_array($api['parts'] ?? null) ? $api['parts'] : [];

        return new self(
            timestamp: is_string($payload['timestamp'] ?? null) ? $payload['timestamp'] : null,
            accountId: (string) ($account['id'] ?? ''),
            workflowExecutionId: (string) ($workflowExecution['id'] ?? ''),
            credentialId: is_string($credential['id'] ?? null) ? $credential['id'] : null,
            partId: is_string($part['id'] ?? null) ? $part['id'] : null,
            apiToken: is_string($api['token'] ?? null) ? $api['token'] : null,
            partUrls: array_filter(
                array_map(static fn (mixed $value): ?string => is_string($value) ? $value : null, $parts),
                static fn (?string $value): bool => $value !== null,
            ),
            workflowExecutionUrl: is_string($api['workflowExecution'] ?? null) ? $api['workflowExecution'] : null,
            raw: $payload,
        );
    }
}
