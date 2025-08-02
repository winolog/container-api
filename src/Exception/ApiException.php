<?php

namespace winolog\ContainerApiClient\Exception;

use Throwable;

class ApiException extends \RuntimeException
{
    private ?array $responseData;
    private ?int $statusCode;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        ?array $responseData = null,
        ?int $statusCode = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseData = $responseData;
        $this->statusCode = $statusCode;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public static function fromRequestError(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
        ?array $responseData = null,
        ?int $statusCode = null
    ): self {
        return new self(
            'API request failed: ' . $message,
            $code,
            $previous,
            $responseData,
            $statusCode
        );
    }
}
