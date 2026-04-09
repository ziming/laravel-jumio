<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\ConsentData;
use Ziming\LaravelJumio\Data\CredentialDefinitionData;
use Ziming\LaravelJumio\Data\PredefinedValueData;
use Ziming\LaravelJumio\Data\UserConsentData;
use Ziming\LaravelJumio\Data\UserLocationData;
use Ziming\LaravelJumio\Data\WebSettingsData;
use Ziming\LaravelJumio\Data\WorkflowDefinitionData;
use Ziming\LaravelJumio\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function jsonFixture(string $path): array
{
    $contents = file_get_contents(__DIR__.'/Fixtures/'.$path);

    if ($contents === false) {
        throw new RuntimeException(sprintf('Unable to read test fixture [%s].', $path));
    }

    /** @var array<string, mixed> $decoded */
    $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

    return $decoded;
}

function mockJsonFixture(string $path, int $status = 200): MockResponse
{
    return MockResponse::make(jsonFixture($path), $status);
}

function recordedRequestCount(string $requestClass): int
{
    $count = 0;

    foreach (Saloon::mockClient()->getRecordedResponses() as $response) {
        if ($response instanceof Response && $response->getRequest() instanceof $requestClass) {
            $count++;
        }
    }

    return $count;
}

function makeAccountRequestData(): AccountRequestData
{
    return new AccountRequestData(
        customerInternalReference: 'customer-123',
        userReference: 'user-123',
        reportingCriteria: 'premium-onboarding',
        workflowDefinition: new WorkflowDefinitionData(
            key: '10547',
            credentials: [
                new CredentialDefinitionData(
                    category: 'ID',
                    country: new PredefinedValueData(['USA']),
                    type: new PredefinedValueData(['PASSPORT']),
                ),
            ],
            capabilities: [
                'documentVerification' => [
                    'enableExtraction' => true,
                ],
            ],
        ),
        web: new WebSettingsData(
            successUrl: 'https://example.test/jumio/success',
            errorUrl: 'https://example.test/jumio/error',
            locale: 'en-US',
        ),
        userConsent: new UserConsentData(
            userLocation: new UserLocationData(country: 'USA', state: 'CA'),
            consent: new ConsentData('yes', '2026-04-10T10:00:00Z'),
            userIp: '203.0.113.5',
        ),
    );
}

function accountResponsePayload(): array
{
    return [
        'timestamp' => '2026-04-10T12:00:00Z',
        'account' => [
            'id' => 'bbf3a261-9bf9-400c-b218-cd275596d8d9',
        ],
        'web' => [
            'href' => 'https://account.amer-1.jumio.ai/sessions/web-123',
            'successUrl' => 'https://example.test/jumio/success',
            'errorUrl' => 'https://example.test/jumio/error',
        ],
        'sdk' => [
            'token' => 'sdk-session-token',
        ],
        'workflowExecution' => [
            'id' => 'be493071-3ffe-4c7f-9bb9-8733a614b627',
            'credentials' => [
                [
                    'id' => 'ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2',
                    'label' => 'primary-id',
                    'category' => 'ID',
                    'allowedChannels' => ['API', 'SDK'],
                    'api' => [
                        'token' => 'transaction-token',
                        'parts' => [
                            'front' => 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627/credentials/ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2/parts/FRONT',
                            'back' => 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627/credentials/ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2/parts/BACK',
                            'face' => 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627/credentials/ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2/parts/FACE',
                            'facemap' => 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627/credentials/ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2/parts/FACEMAP',
                        ],
                        'workflowExecution' => 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627',
                    ],
                ],
            ],
        ],
    ];
}

function credentialUploadResponsePayload(array $overrides = []): array
{
    return array_replace_recursive([
        'timestamp' => '2026-04-10T12:05:00Z',
        'account' => [
            'id' => 'bbf3a261-9bf9-400c-b218-cd275596d8d9',
        ],
        'workflowExecution' => [
            'id' => 'be493071-3ffe-4c7f-9bb9-8733a614b627',
        ],
        'credential' => [
            'id' => 'ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2',
        ],
        'part' => [
            'id' => 'f4de7ae2-8d2d-4ca2-b3f6-2a9720e8ca77',
        ],
        'api' => [
            'token' => 'transaction-token',
            'parts' => [
                'front' => 'https://api.amer-1.jumio.ai/.../parts/FRONT',
                'back' => 'https://api.amer-1.jumio.ai/.../parts/BACK',
            ],
            'workflowExecution' => 'https://api.amer-1.jumio.ai/.../workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627',
        ],
    ], $overrides);
}

function workflowStatusPayload(): array
{
    return [
        'account' => [
            'id' => 'bbf3a261-9bf9-400c-b218-cd275596d8d9',
            'href' => 'https://retrieval.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9',
        ],
        'workflowExecution' => [
            'id' => 'be493071-3ffe-4c7f-9bb9-8733a614b627',
            'href' => 'https://retrieval.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627',
            'definitionKey' => '10547',
            'status' => 'PROCESSED',
        ],
    ];
}

function workflowPdfPayload(): array
{
    return [
        'workflowId' => 'be493071-3ffe-4c7f-9bb9-8733a614b627',
        'presignedUrl' => 'https://example-bucket.s3.amazonaws.com/reports/workflow.pdf',
    ];
}
