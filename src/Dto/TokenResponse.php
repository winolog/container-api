<?php

namespace winolog\ContainerApiClient\Dto;

class TokenResponse
{
    public string $token;
    public int $expiresAt;
    public string $tokenType;

    public function __construct(string $token, int $expiresIn, string $tokenType = 'bearer')
    {
        $this->token = $token;
        $this->expiresAt = time() + $expiresIn;
        $this->tokenType = $tokenType;
    }
}
