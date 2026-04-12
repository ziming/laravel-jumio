<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Throwable;
use Ziming\LaravelJumio\Auth\JumioTokenStore;
use Ziming\LaravelJumio\Connectors\AccountsConnector;
use Ziming\LaravelJumio\Connectors\ApiConnector;
use Ziming\LaravelJumio\Connectors\RetrievalConnector;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\AccountSessionData;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\CredentialUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;
use Ziming\LaravelJumio\Data\WebSettingsData;
use Ziming\LaravelJumio\Data\WorkflowDefinitionData;
use Ziming\LaravelJumio\Data\WorkflowPdfData;
use Ziming\LaravelJumio\Data\WorkflowStatusData;
use Ziming\LaravelJumio\Enums\JumioRegion;
use Ziming\LaravelJumio\Exceptions\JumioException;
use Ziming\LaravelJumio\Exceptions\JumioRequestException;
use Ziming\LaravelJumio\Requests\Accounts\CreateAccountRequest;
use Ziming\LaravelJumio\Requests\Accounts\UpdateAccountRequest;
use Ziming\LaravelJumio\Requests\Credentials\FinalizeWorkflowRequest;
use Ziming\LaravelJumio\Requests\Credentials\UploadCredentialPartRequest;
use Ziming\LaravelJumio\Requests\Retrieval\DownloadImageRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GenerateWorkflowPdfRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowDetailsRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowRulesRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowStatusRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowStepsRequest;

final class JumioManager implements JumioClient
{
    public function __construct(
        private readonly ConfigRepository $config,
        private readonly JumioTokenStore $tokenStore,
    ) {}

    public function createAccount(AccountRequestData $data): AccountSessionData
    {
        $request = new CreateAccountRequest(
            $data->withCallbackUrlIfMissing($this->optionalString('jumio.callback_url')),
        );

        /** @var AccountSessionData $result */
        $result = $this->sendDto($this->accountsConnector(), $request);

        return $result;
    }

    public function createAccountSimple(
        string $customerReference,
        ?string $successUrl = null,
        ?string $errorUrl = null,
        ?string $locale = null,
    ): AccountSessionData {
        $workflowKey = $this->optionalString('jumio.workflow_definition_key');

        if ($workflowKey === null) {
            throw new JumioException('Missing Jumio configuration value [jumio.workflow_definition_key]. Set it in config or use createAccount() with an explicit WorkflowDefinitionData.');
        }

        $web = new WebSettingsData(
            successUrl: $successUrl ?? $this->optionalString('jumio.web.success_url'),
            errorUrl: $errorUrl ?? $this->optionalString('jumio.web.error_url'),
            locale: $locale ?? $this->optionalString('jumio.web.locale'),
        );

        return $this->createAccount(new AccountRequestData(
            customerInternalReference: $customerReference,
            workflowDefinition: new WorkflowDefinitionData(key: $workflowKey),
            web: $web,
        ));
    }

    public function updateAccount(string $accountId, AccountRequestData $data): AccountSessionData
    {
        $request = new UpdateAccountRequest(
            $accountId,
            $data->withCallbackUrlIfMissing($this->optionalString('jumio.callback_url')),
        );

        /** @var AccountSessionData $result */
        $result = $this->sendDto($this->accountsConnector(), $request);

        return $result;
    }

    public function uploadCredentialPart(CredentialPartUploadData $data): CredentialUploadData
    {
        /** @var CredentialUploadData $result */
        $result = $this->sendDto(
            $this->apiConnector($data->token),
            new UploadCredentialPartRequest($data),
        );

        return $result;
    }

    public function finalizeWorkflow(FinalizeWorkflowData $data): CredentialUploadData
    {
        /** @var CredentialUploadData $result */
        $result = $this->sendDto(
            $this->apiConnector($data->token),
            new FinalizeWorkflowRequest($data),
        );

        return $result;
    }

    public function getWorkflowStatus(string $accountId, string $workflowExecutionId): WorkflowStatusData
    {
        /** @var WorkflowStatusData $result */
        $result = $this->sendDto(
            $this->retrievalConnector(),
            new GetWorkflowStatusRequest($accountId, $workflowExecutionId),
        );

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowDetails(string $accountId, string $workflowExecutionId): array
    {
        return $this->sendJson(
            $this->retrievalConnector(),
            new GetWorkflowDetailsRequest($accountId, $workflowExecutionId),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowSteps(string $accountId, string $workflowExecutionId): array
    {
        return $this->sendJson(
            $this->retrievalConnector(),
            new GetWorkflowStepsRequest($accountId, $workflowExecutionId),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getWorkflowRules(string $accountId, string $workflowExecutionId): array
    {
        return $this->sendJson(
            $this->retrievalConnector(),
            new GetWorkflowRulesRequest($accountId, $workflowExecutionId),
        );
    }

    public function generateWorkflowPdf(string $accountId, string $workflowExecutionId): WorkflowPdfData
    {
        /** @var WorkflowPdfData $result */
        $result = $this->sendDto(
            $this->retrievalConnector(),
            new GenerateWorkflowPdfRequest($accountId, $workflowExecutionId),
        );

        return $result;
    }

    public function downloadImage(string $href): ?string
    {
        $host = parse_url($href, PHP_URL_HOST);

        if (! is_string($host) || (! str_ends_with($host, '.jumio.ai') && ! str_ends_with($host, '.jumio.com'))) {
            return null;
        }

        $path = parse_url($href, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return null;
        }

        $query = parse_url($href, PHP_URL_QUERY);

        if (is_string($query) && $query !== '') {
            $path .= '?'.$query;
        }

        try {
            $response = $this->retrievalConnector()->send(new DownloadImageRequest($path));
        } catch (Throwable) {
            return null;
        }

        return $response->successful() ? $response->body() : null;
    }

    public function validateWebhookSignature(string $rawBody, string $signature): bool
    {
        $secret = $this->requiredString('jumio.client_secret');
        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, strtolower($signature));
    }

    private function sendDto(Connector $connector, Request $request): mixed
    {
        $response = $this->send($connector, $request);

        return $response->dto();
    }

    /**
     * @return array<string, mixed>
     */
    private function sendJson(Connector $connector, Request $request): array
    {
        $response = $this->send($connector, $request);

        /** @var array<string, mixed> $payload */
        $payload = $response->json();

        return $payload;
    }

    private function send(Connector $connector, Request $request): Response
    {
        try {
            $response = $connector->send($request);
        } catch (Throwable $throwable) {
            throw JumioException::fromThrowable($throwable);
        }

        if ($response->failed()) {
            throw JumioRequestException::fromResponse($response);
        }

        return $response;
    }

    private function accountsConnector(): AccountsConnector
    {
        return new AccountsConnector(
            region: $this->region(),
            accessToken: $this->tokenStore->getAccessToken($this->region()),
            userAgent: $this->requiredString('jumio.user_agent'),
            timeout: $this->intConfig('jumio.timeout', 30),
            connectTimeout: $this->intConfig('jumio.connect_timeout', 10),
            tries: $this->intConfig('jumio.retry.tries', 1),
            retryInterval: $this->intConfig('jumio.retry.interval_ms', 250),
            useExponentialBackoff: (bool) $this->config->get('jumio.retry.exponential_backoff', false),
        );
    }

    private function apiConnector(string $token): ApiConnector
    {
        return new ApiConnector(
            region: $this->region(),
            transactionToken: $token,
            userAgent: $this->optionalString('jumio.user_agent'),
            timeout: $this->intConfig('jumio.timeout', 30),
            connectTimeout: $this->intConfig('jumio.connect_timeout', 10),
            tries: $this->intConfig('jumio.retry.tries', 1),
            retryInterval: $this->intConfig('jumio.retry.interval_ms', 250),
            useExponentialBackoff: (bool) $this->config->get('jumio.retry.exponential_backoff', false),
        );
    }

    private function retrievalConnector(): RetrievalConnector
    {
        return new RetrievalConnector(
            region: $this->region(),
            accessToken: $this->tokenStore->getAccessToken($this->region()),
            userAgent: $this->optionalString('jumio.user_agent'),
            timeout: $this->intConfig('jumio.timeout', 30),
            connectTimeout: $this->intConfig('jumio.connect_timeout', 10),
            tries: $this->intConfig('jumio.retry.tries', 1),
            retryInterval: $this->intConfig('jumio.retry.interval_ms', 250),
            useExponentialBackoff: (bool) $this->config->get('jumio.retry.exponential_backoff', false),
        );
    }

    private function region(): JumioRegion
    {
        return JumioRegion::fromConfig((string) $this->config->get('jumio.region', 'amer-1'));
    }

    private function requiredString(string $key): string
    {
        $value = $this->optionalString($key);

        if ($value === null) {
            throw new JumioException(sprintf('Missing Jumio configuration value [%s].', $key));
        }

        return $value;
    }

    private function optionalString(string $key): ?string
    {
        $value = $this->config->get($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    private function intConfig(string $key, int $default): int
    {
        return max(0, (int) $this->config->get($key, $default));
    }
}
