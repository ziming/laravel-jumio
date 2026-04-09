<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Retrieval;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Ziming\LaravelJumio\Data\WorkflowStatusData;

final class GetWorkflowStatusRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $accountId,
        private readonly string $workflowExecutionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/api/v1/accounts/%s/workflow-executions/%s/status',
            $this->accountId,
            $this->workflowExecutionId,
        );
    }

    public function createDtoFromResponse(Response $response): WorkflowStatusData
    {
        return WorkflowStatusData::fromArray($response->json());
    }
}
