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

    /**
     * Create an account using config defaults for workflow key and web settings.
     *
     * Uses jumio.workflow_definition_key, jumio.web.success_url, jumio.web.error_url,
     * and jumio.web.locale from config. Pass explicit values to override.
     */
    public function createAccountSimple(
        string $customerReference,
        ?string $successUrl = null,
        ?string $errorUrl = null,
        ?string $locale = null,
    ): AccountSessionData;

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

    /**
     * Download image content from a Jumio retrieval URL.
     *
     * Returns the raw binary content on success, or null if the
     * download failed or the URL points to a non-Jumio host.
     */
    public function downloadImage(string $href): ?string;

    /**
     * Validate an incoming Jumio webhook signature (HMAC-SHA256).
     */
    public function validateWebhookSignature(string $rawBody, string $signature): bool;
}
