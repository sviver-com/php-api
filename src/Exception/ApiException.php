<?php

declare(strict_types = 1);

namespace Sviver\Api\Exception;

use Exception;
use GuzzleHttp\Psr7\Response;
use Throwable;

/**
 * Base exception
 */
class ApiException extends Exception
{

    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null,
        public readonly ?Response $response = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}