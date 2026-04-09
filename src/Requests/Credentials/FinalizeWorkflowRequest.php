<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Credentials;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Ziming\LaravelJumio\Data\CredentialUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;

final class FinalizeWorkflowRequest extends Request
{
    protected Method $method = Method::PUT;

    public function __construct(
        private readonly FinalizeWorkflowData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/api/v1/accounts/%s/workflow-executions/%s',
            $this->data->accountId,
            $this->data->workflowExecutionId,
        );
    }

    public function createDtoFromResponse(Response $response): CredentialUploadData
    {
        return CredentialUploadData::fromArray($response->json());
    }
}
