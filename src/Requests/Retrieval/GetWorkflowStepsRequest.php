<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Retrieval;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetWorkflowStepsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $accountId,
        private readonly string $workflowExecutionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/api/v1/accounts/%s/workflow-executions/%s/steps',
            $this->accountId,
            $this->workflowExecutionId,
        );
    }
}
