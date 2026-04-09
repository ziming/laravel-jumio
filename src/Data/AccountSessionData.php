<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class AccountSessionData extends Data
{
    /**
     * @param  array<int, WorkflowCredentialData>  $credentials
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public ?string $timestamp,
        public string $accountId,
        public ?string $webHref,
        public ?string $successUrl,
        public ?string $errorUrl,
        public ?string $sdkToken,
        public string $workflowExecutionId,
        #[DataCollectionOf(WorkflowCredentialData::class)]
        public array $credentials,
        public array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $account = is_array($payload['account'] ?? null) ? $payload['account'] : [];
        $web = is_array($payload['web'] ?? null) ? $payload['web'] : [];
        $sdk = is_array($payload['sdk'] ?? null) ? $payload['sdk'] : [];
        $workflowExecution = is_array($payload['workflowExecution'] ?? null) ? $payload['workflowExecution'] : [];
        $credentials = is_array($workflowExecution['credentials'] ?? null) ? $workflowExecution['credentials'] : [];

        return new self(
            timestamp: is_string($payload['timestamp'] ?? null) ? $payload['timestamp'] : null,
            accountId: (string) ($account['id'] ?? ''),
            webHref: is_string($web['href'] ?? null) ? $web['href'] : null,
            successUrl: is_string($web['successUrl'] ?? null) ? $web['successUrl'] : null,
            errorUrl: is_string($web['errorUrl'] ?? null) ? $web['errorUrl'] : null,
            sdkToken: is_string($sdk['token'] ?? null) ? $sdk['token'] : null,
            workflowExecutionId: (string) ($workflowExecution['id'] ?? ''),
            credentials: array_map(
                static fn (array $credential): WorkflowCredentialData => WorkflowCredentialData::fromArray($credential),
                array_filter($credentials, 'is_array'),
            ),
            raw: $payload,
        );
    }

    public function firstCredential(): ?WorkflowCredentialData
    {
        return $this->credentials[0] ?? null;
    }

    public function findCredential(string $credentialId): ?WorkflowCredentialData
    {
        foreach ($this->credentials as $credential) {
            if ($credential->id === $credentialId) {
                return $credential;
            }
        }

        return null;
    }
}
