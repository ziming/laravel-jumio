<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class WorkflowStatusData extends Data
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $accountId,
        public ?string $accountHref,
        public string $workflowExecutionId,
        public ?string $workflowExecutionHref,
        public ?string $definitionKey,
        public ?string $status,
        public array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $account = is_array($payload['account'] ?? null) ? $payload['account'] : [];
        $workflowExecution = is_array($payload['workflowExecution'] ?? null) ? $payload['workflowExecution'] : [];

        return new self(
            accountId: (string) ($account['id'] ?? ''),
            accountHref: is_string($account['href'] ?? null) ? $account['href'] : null,
            workflowExecutionId: (string) ($workflowExecution['id'] ?? ''),
            workflowExecutionHref: is_string($workflowExecution['href'] ?? null) ? $workflowExecution['href'] : null,
            definitionKey: is_string($workflowExecution['definitionKey'] ?? null) ? $workflowExecution['definitionKey'] : null,
            status: is_string($workflowExecution['status'] ?? null) ? $workflowExecution['status'] : null,
            raw: $payload,
        );
    }
}
