<?php

declare(strict_types=1);

namespace Ziming\LaravelJumio\Exceptions;

use Saloon\Http\Response;
use Throwable;

class JumioRequestException extends JumioException
{
    /**
     * @param  array<string, mixed>  $problem
     */
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly array $problem = [],
        public readonly ?Response $response = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public static function fromResponse(Response $response, ?Throwable $previous = null): self
    {
        $problem = [];

        try {
            /** @var array<string, mixed> $json */
            $json = $response->json();
            $problem = $json;
        } catch (Throwable) {
            //
        }

        $status = $response->status();
        $title = is_string($problem['title'] ?? null) ? $problem['title'] : 'Jumio request failed';
        $detail = is_string($problem['detail'] ?? null) ? $problem['detail'] : null;
        $message = sprintf('%s (HTTP %d)', $title, $status);

        if ($detail !== null && $detail !== '') {
            $message .= sprintf(': %s', $detail);
        }

        return new self($message, $status, $problem, $response, $previous);
    }
}
