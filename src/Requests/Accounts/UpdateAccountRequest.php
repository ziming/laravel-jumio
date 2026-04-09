<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Accounts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Ziming\LaravelJumio\Data\AccountRequestData;
use Ziming\LaravelJumio\Data\AccountSessionData;

final class UpdateAccountRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        private readonly string $accountId,
        private readonly AccountRequestData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return sprintf('/api/v1/accounts/%s', $this->accountId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    public function createDtoFromResponse(Response $response): AccountSessionData
    {
        return AccountSessionData::fromArray($response->json());
    }
}
