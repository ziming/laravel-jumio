<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class CredentialPartUploadData extends Data
{
    public function __construct(
        public string $accountId,
        public string $workflowExecutionId,
        public string $credentialId,
        public string $token,
        public string $classifier,
        public mixed $file,
        public ?string $filename = null,
        public bool $replace = false,
    ) {}
}
