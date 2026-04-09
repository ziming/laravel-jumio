<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Requests\Auth\RequestAccessTokenRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GenerateWorkflowPdfRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowDetailsRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowRulesRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowStatusRequest;
use Ziming\LaravelJumio\Requests\Retrieval\GetWorkflowStepsRequest;

it('retrieves workflow status, details, steps, rules, and pdf links', function () {
    Saloon::fake([
        RequestAccessTokenRequest::class => MockResponse::make([
            'access_token' => 'oauth-access-token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]),
        GetWorkflowStatusRequest::class => MockResponse::make(workflowStatusPayload()),
        GetWorkflowDetailsRequest::class => mockJsonFixture('retrieval/details.json'),
        GetWorkflowStepsRequest::class => mockJsonFixture('retrieval/steps.json'),
        GetWorkflowRulesRequest::class => mockJsonFixture('retrieval/rules.json'),
        GenerateWorkflowPdfRequest::class => MockResponse::make(workflowPdfPayload()),
    ]);

    $client = app(JumioClient::class);

    $status = $client->getWorkflowStatus('bbf3a261-9bf9-400c-b218-cd275596d8d9', 'be493071-3ffe-4c7f-9bb9-8733a614b627');
    $details = $client->getWorkflowDetails('bbf3a261-9bf9-400c-b218-cd275596d8d9', 'be493071-3ffe-4c7f-9bb9-8733a614b627');
    $steps = $client->getWorkflowSteps('bbf3a261-9bf9-400c-b218-cd275596d8d9', 'be493071-3ffe-4c7f-9bb9-8733a614b627');
    $rules = $client->getWorkflowRules('bbf3a261-9bf9-400c-b218-cd275596d8d9', 'be493071-3ffe-4c7f-9bb9-8733a614b627');
    $pdf = $client->generateWorkflowPdf('bbf3a261-9bf9-400c-b218-cd275596d8d9', 'be493071-3ffe-4c7f-9bb9-8733a614b627');

    expect($status->status)->toBe('PROCESSED')
        ->and($details['workflowExecution']['status'])->toBe('PROCESSED')
        ->and($steps['steps'][0]['status'])->toBe('PROCESSED')
        ->and($rules['rules'][0]['outcome'])->toBe('PASSED')
        ->and($pdf->presignedUrl)->toContain('workflow.pdf');
});
