<?php

declare(strict_types=1);

use Saloon\Data\MultipartValue;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Ziming\LaravelJumio\Contracts\JumioClient;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\FinalizeWorkflowData;
use Ziming\LaravelJumio\Requests\Credentials\FinalizeWorkflowRequest;
use Ziming\LaravelJumio\Requests\Credentials\UploadCredentialPartRequest;

it('uploads a credential part with the transaction token and multipart body', function () {
    Saloon::fake([
        UploadCredentialPartRequest::class => MockResponse::make(credentialUploadResponsePayload(), 201),
    ]);

    $result = app(JumioClient::class)->uploadCredentialPart(new CredentialPartUploadData(
        accountId: 'bbf3a261-9bf9-400c-b218-cd275596d8d9',
        workflowExecutionId: 'be493071-3ffe-4c7f-9bb9-8733a614b627',
        credentialId: 'ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2',
        token: 'transaction-token',
        classifier: 'front',
        file: 'front-binary',
        filename: 'front.png',
    ));

    expect($result->partId)->toBe('f4de7ae2-8d2d-4ca2-b3f6-2a9720e8ca77');

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof UploadCredentialPartRequest) {
            return false;
        }

        $parts = $response->getPendingRequest()->body()?->all();
        $part = $parts[0] ?? null;

        return $response->getPendingRequest()->headers()->get('Authorization') === 'Bearer transaction-token'
            && $response->getPendingRequest()->getUrl() === 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627/credentials/ff0a45e1-9f85-470e-8cfb-e478ef1a3ff2/parts/FRONT'
            && $part instanceof MultipartValue
            && $part->filename === 'front.png'
            && $part->value === 'front-binary';
    });
});

it('finalizes the workflow with the transaction token', function () {
    Saloon::fake([
        FinalizeWorkflowRequest::class => MockResponse::make(
            credentialUploadResponsePayload(['part' => null]),
        ),
    ]);

    $result = app(JumioClient::class)->finalizeWorkflow(new FinalizeWorkflowData(
        accountId: 'bbf3a261-9bf9-400c-b218-cd275596d8d9',
        workflowExecutionId: 'be493071-3ffe-4c7f-9bb9-8733a614b627',
        token: 'transaction-token',
    ));

    expect($result->workflowExecutionId)->toBe('be493071-3ffe-4c7f-9bb9-8733a614b627')
        ->and($result->partId)->toBeNull();

    Saloon::assertSent(function ($request, $response) {
        if (! $request instanceof FinalizeWorkflowRequest) {
            return false;
        }

        return $response->getPendingRequest()->headers()->get('Authorization') === 'Bearer transaction-token'
            && $response->getPendingRequest()->getUrl() === 'https://api.amer-1.jumio.ai/api/v1/accounts/bbf3a261-9bf9-400c-b218-cd275596d8d9/workflow-executions/be493071-3ffe-4c7f-9bb9-8733a614b627';
    });
});
