<?php

namespace winolog\ContainerApiClient\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use winolog\ContainerApiClient\Dto\TokenResponse;
use winolog\ContainerApiClient\Exception\ApiException;
use winolog\ContainerApiClient\Exception\TokenStorageException;

class JwtAuthenticator
{
    private string $apiBaseUrl;
    private string $login;
    private string $password;
    private ?TokenResponse $tokenResponse = null;
    private ?TokenStorageInterface $tokenStorage = null;
    private ?string $csrfToken = null;

    public function __construct(
        string $apiBaseUrl,
        string $login,
        string $password,
        ?TokenStorageInterface $tokenStorage = null,
        ?string $csrfToken = null
    ) {
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/');
        $this->login = $login;
        $this->password = $password;
        $this->tokenStorage = $tokenStorage;
        $this->csrfToken = $csrfToken;
    }

    /**
     * @throws ApiException
     */
    public function authenticate(): TokenResponse
    {
        try {
            // Попробуем загрузить токен из хранилища
            if ($this->tokenStorage !== null) {
                $this->tokenResponse = $this->tokenStorage->loadToken();
                if ($this->tokenResponse && !$this->isTokenExpired($this->tokenResponse)) {
                    return $this->tokenResponse;
                }
            }

            // Если токена нет или он просрочен, запрашиваем новый
            $client = new Client();

            $headers = [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            if ($this->csrfToken !== null) {
                $headers['X-CSRF-TOKEN'] = $this->csrfToken;
            }

            $response = $client->post($this->apiBaseUrl . '/token/login', [
                'headers' => $headers,
                'json' => [
                    'login' => $this->login,
                    'password' => $this->password
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $this->tokenResponse = new TokenResponse(
                $data['access_token'],
                $data['expires_in'],
                $data['token_type'] ?? 'bearer'
            );

            // Сохраняем токен в хранилище
            if ($this->tokenStorage !== null) {
                try {
                    $this->tokenStorage->saveToken($this->tokenResponse);
                } catch (TokenStorageException $e) {
                    // Логируем ошибку сохранения, но не прерываем выполнение
                    error_log('Failed to save token: ' . $e->getMessage());
                }
            }

            return $this->tokenResponse;
        } catch (GuzzleException $e) {
            throw new ApiException('Authentication failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getToken(): ?string
    {
        return $this->tokenResponse?->token;
    }

    public function isTokenExpired(?TokenResponse $tokenResponse = null): bool
    {
        $token = $tokenResponse ?? $this->tokenResponse;
        if (!$token) {
            return true;
        }

        // Добавляем буфер в 60 секунд, чтобы избежать проблем с граничными случаями
        return ($token->expiresAt - 60) < time();
    }

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }
}
