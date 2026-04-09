<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data;

use Spatie\LaravelData\Data;
use Ziming\LaravelJumio\Data\Concerns\NormalizesPayload;

final class AccountRequestData extends Data
{
    use NormalizesPayload;

    /**
     * @param  array<string, string>|null  $overrideSettings
     */
    public function __construct(
        public string $customerInternalReference,
        public WorkflowDefinitionData $workflowDefinition,
        public ?string $userReference = null,
        public ?string $reportingCriteria = null,
        public ?string $callbackUrl = null,
        public ?string $tokenLifetime = null,
        public ?WebSettingsData $web = null,
        public ?UserConsentData $userConsent = null,
        public ?array $overrideSettings = null,
    ) {}

    public function withCallbackUrlIfMissing(?string $callbackUrl): self
    {
        if ($this->callbackUrl !== null || $callbackUrl === null) {
            return $this;
        }

        return new self(
            customerInternalReference: $this->customerInternalReference,
            workflowDefinition: $this->workflowDefinition,
            userReference: $this->userReference,
            reportingCriteria: $this->reportingCriteria,
            callbackUrl: $callbackUrl,
            tokenLifetime: $this->tokenLifetime,
            web: $this->web,
            userConsent: $this->userConsent,
            overrideSettings: $this->overrideSettings,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::normalizePayload(parent::toArray());
    }
}
