<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Requests\Retrieval;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DownloadImageRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $path,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->path;
    }
}
