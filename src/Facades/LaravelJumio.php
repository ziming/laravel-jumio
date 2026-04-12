<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Facades;

use Illuminate\Support\Facades\Facade;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\AccountSessionData;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\CredentialUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;
use Ziming\LaravelJumio\Data\WorkflowPdfData;
use Ziming\LaravelJumio\Data\WorkflowStatusData;
use Ziming\LaravelJumio\JumioManager;

/**
 * @method static AccountSessionData createAccount(AccountRequestData $data)
 * @method static AccountSessionData createAccountSimple(string $customerReference, ?string $successUrl = null, ?string $errorUrl = null, ?string $locale = null)
 * @method static AccountSessionData updateAccount(string $accountId, AccountRequestData $data)
 * @method static CredentialUploadData uploadCredentialPart(CredentialPartUploadData $data)
 * @method static CredentialUploadData finalizeWorkflow(FinalizeWorkflowData $data)
 * @method static WorkflowStatusData getWorkflowStatus(string $accountId, string $workflowExecutionId)
 * @method static array getWorkflowDetails(string $accountId, string $workflowExecutionId)
 * @method static array getWorkflowSteps(string $accountId, string $workflowExecutionId)
 * @method static array getWorkflowRules(string $accountId, string $workflowExecutionId)
 * @method static WorkflowPdfData generateWorkflowPdf(string $accountId, string $workflowExecutionId)
 * @method static string|null downloadImage(string $href)
 * @method static bool validateWebhookSignature(string $rawBody, string $signature)
 *
 * @see JumioManager
 */
class LaravelJumio extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JumioClient::class;
    }
}
