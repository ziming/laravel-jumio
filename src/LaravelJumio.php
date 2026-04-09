<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio;

use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\AccountSessionData;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\CredentialUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;
use Ziming\LaravelJumio\Data\WorkflowPdfData;
use Ziming\LaravelJumio\Data\WorkflowStatusData;

/**
 * Backwards-compatible wrapper around the real manager binding.
 */
class LaravelJumio implements JumioClient
{
    public function __construct(
        private readonly JumioClient $client,
    ) {}

    public function createAccount(AccountRequestData $data): AccountSessionData
    {
        return $this->client->createAccount($data);
    }

    public function updateAccount(string $accountId, AccountRequestData $data): AccountSessionData
    {
        return $this->client->updateAccount($accountId, $data);
    }

    public function uploadCredentialPart(CredentialPartUploadData $data): CredentialUploadData
    {
        return $this->client->uploadCredentialPart($data);
    }

    public function finalizeWorkflow(FinalizeWorkflowData $data): CredentialUploadData
    {
        return $this->client->finalizeWorkflow($data);
    }

    public function getWorkflowStatus(string $accountId, string $workflowExecutionId): WorkflowStatusData
    {
        return $this->client->getWorkflowStatus($accountId, $workflowExecutionId);
    }

    public function getWorkflowDetails(string $accountId, string $workflowExecutionId): array
    {
        return $this->client->getWorkflowDetails($accountId, $workflowExecutionId);
    }

    public function getWorkflowSteps(string $accountId, string $workflowExecutionId): array
    {
        return $this->client->getWorkflowSteps($accountId, $workflowExecutionId);
    }

    public function getWorkflowRules(string $accountId, string $workflowExecutionId): array
    {
        return $this->client->getWorkflowRules($accountId, $workflowExecutionId);
    }

    public function generateWorkflowPdf(string $accountId, string $workflowExecutionId): WorkflowPdfData
    {
        return $this->client->generateWorkflowPdf($accountId, $workflowExecutionId);
    }
}
