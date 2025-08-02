<?php

namespace winolog\ContainerApiClient\Auth;

use winolog\ContainerApiClient\Dto\TokenResponse;
use winolog\ContainerApiClient\Exception\TokenStorageException;

interface TokenStorageInterface
{
    /**
     * @throws TokenStorageException
     */
    public function saveToken(TokenResponse $tokenResponse): void;

    /**
     * @throws TokenStorageException
     */
    public function loadToken(): ?TokenResponse;

    /**
     * @throws TokenStorageException
     */
    public function clearToken(): void;
}
