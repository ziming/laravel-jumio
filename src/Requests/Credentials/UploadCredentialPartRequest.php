<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Credentials;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasMultipartBody;
use Ziming\LaravelJumio\Data\CredentialPartUploadData;
use Ziming\LaravelJumio\Data\CredentialUploadData;

final class UploadCredentialPartRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method;

    public function __construct(
        private readonly CredentialPartUploadData $data,
    ) {
        $this->method = $data->replace ? Method::PUT : Method::POST;
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/api/v1/accounts/%s/workflow-executions/%s/credentials/%s/parts/%s',
            $this->data->accountId,
            $this->data->workflowExecutionId,
            $this->data->credentialId,
            strtoupper($this->data->classifier),
        );
    }

    /**
     * @return array<int, MultipartValue>
     */
    protected function defaultBody(): array
    {
        $value = $this->data->file;
        $filename = $this->data->filename;

        if (is_string($value) && is_file($value)) {
            $filename ??= basename($value);
            $value = fopen($value, 'rb');
        }

        return [
            new MultipartValue('file', $value, $filename),
        ];
    }

    public function createDtoFromResponse(Response $response): CredentialUploadData
    {
        return CredentialUploadData::fromArray($response->json());
    }
}
