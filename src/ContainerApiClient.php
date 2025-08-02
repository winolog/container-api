<?php

namespace winolog\ContainerApiClient;

use Dotenv\Dotenv;
use winolog\ContainerApiClient\Auth\FileTokenStorage;
use winolog\ContainerApiClient\Auth\JwtAuthenticator;
use winolog\ContainerApiClient\Service\ContainerService;
use winolog\ContainerApiClient\Service\ReferenceService;

class ContainerApiClient
{
    private JwtAuthenticator $authenticator;
    private ContainerService $containerService;
    private ReferenceService $referenceService;
    private string $apiBaseUrl;

    public function __construct(?string $tokenStoragePath = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../');
        $dotenv->load();

        $this->apiBaseUrl = $_ENV['CONTAINER_API_BASE_URL'] ?? 'http://localhost:8085';
        $login = $_ENV['CONTAINER_API_LOGIN'] ?? '';
        $password = $_ENV['CONTAINER_API_PASSWORD'] ?? '';
        $csrfToken = $_ENV['CONTAINER_API_CSRF_TOKEN'] ?? null;

        // Настройка хранилища токенов
        $storage = null;
        if ($tokenStoragePath !== null || isset($_ENV['CONTAINER_API_TOKEN_STORAGE_PATH'])) {
            $path = $tokenStoragePath ?? $_ENV['CONTAINER_API_TOKEN_STORAGE_PATH'];
            $storage = new FileTokenStorage($path);
        }

        $this->authenticator = new JwtAuthenticator(
            $this->apiBaseUrl,
            $login,
            $password,
            $storage,
            $csrfToken
        );

        $this->containerService = new ContainerService($this->authenticator, $this->apiBaseUrl);
        $this->referenceService = new ReferenceService($this->authenticator, $this->apiBaseUrl);
    }

    public function containers(): ContainerService
    {
        return $this->containerService;
    }

    public function references(): ReferenceService
    {
        if (!isset($this->referenceService)) {
            $this->referenceService = new ReferenceService($this->authenticator, $this->apiBaseUrl);
        }
        return $this->referenceService;
    }

    public function getAuthenticator(): JwtAuthenticator
    {
        return $this->authenticator;
    }

    public function uploadContainerPhoto(int $containerId, string $photoPath, ?string $description = null): array
    {
        return $this->containerService->uploadContainerPhoto($containerId, $photoPath, $description);
    }
}
