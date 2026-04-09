<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;

final class FinalizeWorkflowData extends Data
{
    public function __construct(
        public string $accountId,
        public string $workflowExecutionId,
        public string $token,
    ) {}
}
