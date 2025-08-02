<?php

namespace winolog\ContainerApiClient\Service;

use GuzzleHttp\Exception\GuzzleException;
use winolog\ContainerApiClient\Auth\JwtAuthenticator;
use winolog\ContainerApiClient\Exception\ApiException;

class ReferenceService
{
    private $client;
    private $authenticator;
    private $apiBaseUrl;

    public function __construct(JwtAuthenticator $authenticator, string $apiBaseUrl)
    {
        $this->authenticator = $authenticator;
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/');
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Базовый метод для запроса справочников
     * @throws ApiException
     */
    private function getReferenceData(string $endpoint): array
    {
        $this->ensureAuthenticated();

        try {
            $response = $this->client->get($this->apiBaseUrl . $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authenticator->getToken(),
                    'Accept' => 'application/json',
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ApiException("Failed to get reference data: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    // Специфичные методы для каждого справочника

    public function getTerminals(): array
    {
        return $this->getReferenceData('/terminals');
    }

    public function getContainerSizes(): array
    {
        return $this->getReferenceData('/container-sizes');
    }

    public function getContainerTypes(): array
    {
        return $this->getReferenceData('/container-types');
    }

    public function getCoolers(): array
    {
        return $this->getReferenceData('/coolers');
    }

    public function getCoolerModels(): array
    {
        return $this->getReferenceData('/cooler-models');
    }

    public function getCurrencies(): array
    {
        return $this->getReferenceData('/currencies');
    }

    public function getSpecials(): array
    {
        return $this->getReferenceData('/specials');
    }

    public function getContainerQualities(): array
    {
        return $this->getReferenceData('/container-qualities');
    }

    /**
     * @throws ApiException
     */
    private function ensureAuthenticated(): void
    {
        if ($this->authenticator->isTokenExpired()) {
            $this->authenticator->authenticate();
        }
    }
}
