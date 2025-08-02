<?php

namespace winolog\ContainerApiClient\Auth;

use winolog\ContainerApiClient\Dto\TokenResponse;
use winolog\ContainerApiClient\Exception\TokenStorageException;

class FileTokenStorage implements TokenStorageInterface
{
    private string $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
    }

    public function saveToken(TokenResponse $tokenResponse): void
    {
        try {
            $data = serialize($tokenResponse);
            if (file_put_contents($this->storagePath, $data) === false) {
                throw new TokenStorageException('Failed to write token to file');
            }
        } catch (\Exception $e) {
            throw new TokenStorageException('Token storage error: ' . $e->getMessage());
        }
    }

    public function loadToken(): ?TokenResponse
    {
        if (!file_exists($this->storagePath)) {
            return null;
        }

        try {
            $data = file_get_contents($this->storagePath);
            if ($data === false) {
                throw new TokenStorageException('Failed to read token file');
            }

            $token = unserialize($data);
            return $token instanceof TokenResponse ? $token : null;
        } catch (\Exception $e) {
            throw new TokenStorageException('Token load error: ' . $e->getMessage());
        }
    }

    public function clearToken(): void
    {
        if (file_exists($this->storagePath)) {
            unlink($this->storagePath);
        }
    }
}
