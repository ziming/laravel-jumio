<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Exceptions;

use RuntimeException;
use Throwable;

class JumioException extends RuntimeException
{
    public static function fromThrowable(Throwable $throwable, ?string $message = null): self
    {
        if ($throwable instanceof self) {
            return $throwable;
        }

        return new self($message ?? $throwable->getMessage(), (int) $throwable->getCode(), $throwable);
    }
}
