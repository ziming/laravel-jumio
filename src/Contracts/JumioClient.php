<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Contracts;

use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\AccountSessionData;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\CredentialUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;
use Ziming\LaravelJumio\Data\WorkflowPdfData;
use Ziming\LaravelJumio\Data\WorkflowStatusData;

interface JumioClient
{
    public function createAccount(AccountRequestData $data): AccountSessionData;

    public function updateAccount(string $accountId, AccountRequestData $data): AccountSessionData;

    public function uploadCredentialPart(CredentialPartUploadData $data): CredentialUploadData;

    public function finalizeWorkflow(FinalizeWorkflowData $data): CredentialUploadData;

    public function getWorkflowStatus(string $accountId, string $workflowExecutionId): WorkflowStatusData;

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowDetails(string $accountId, string $workflowExecutionId): array;

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowSteps(string $accountId, string $workflowExecutionId): array;

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowRules(string $accountId, string $workflowExecutionId): array;

    public function generateWorkflowPdf(string $accountId, string $workflowExecutionId): WorkflowPdfData;
}
