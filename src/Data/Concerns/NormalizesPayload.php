<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Data\Concerns;

trait NormalizesPayload
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function normalizePayload(array $payload): array
    {
        $normalized = [];

        foreach ($payload as $key => $value) {
            $normalizedValue = static::normalizeValue($value);

            if ($normalizedValue === null) {
                continue;
            }

            $normalized[$key] = $normalizedValue;
        }

        return $normalized;
    }

    protected static function normalizeValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (! is_array($value)) {
            return $value;
        }

        $normalized = [];

        foreach ($value as $key => $item) {
            $normalizedItem = static::normalizeValue($item);

            if ($normalizedItem === null) {
                continue;
            }

            $normalized[$key] = $normalizedItem;
        }

        return $normalized;
    }
}
