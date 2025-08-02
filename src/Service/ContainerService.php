<?php

namespace winolog\ContainerApiClient\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use winolog\ContainerApiClient\Auth\JwtAuthenticator;
use winolog\ContainerApiClient\Dto\ContainerDto;
use winolog\ContainerApiClient\Exception\ApiException;

class ContainerService
{
    private Client $client;
    private JwtAuthenticator $authenticator;
    private string $apiBaseUrl;

    public function __construct(JwtAuthenticator $authenticator, string $apiBaseUrl)
    {
        $this->authenticator = $authenticator;
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/');
        $this->client = new Client();
    }

    /**
     * @throws ApiException
     */
    public function createContainer(ContainerDto $containerDto): array
    {
        $this->ensureAuthenticated();

        try {
            $response = $this->client->post($this->apiBaseUrl . '/api/containers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authenticator->getToken(),
                    'Accept' => 'application/json',
                ],
                'json' => $containerDto->toArray()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleRequestException($e, 'Failed to create container');
        } catch (GuzzleException $e) {
            throw new ApiException('Network error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ApiException
     */
    public function uploadContainerPhoto(int $containerId, string $photoPath, ?string $description = null): array
    {
        $this->ensureAuthenticated();

        if (!file_exists($photoPath)) {
            throw new ApiException("Photo file not found: $photoPath");
        }

        try {
            $multipart = [
                [
                    'name' => 'photo',
                    'contents' => fopen($photoPath, 'r'),
                    'filename' => basename($photoPath),
                    'headers' => [
                        'Content-Type' => mime_content_type($photoPath)
                    ]
                ]
            ];

            if ($description !== null) {
                $multipart[] = [
                    'name' => 'description',
                    'contents' => $description
                ];
            }

            $response = $this->client->post(
                $this->apiBaseUrl . "/api/containers/$containerId/photos",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->authenticator->getToken(),
                        'Accept' => 'application/json',
                    ],
                    'multipart' => $multipart
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $this->handleRequestException($e, 'Failed to upload container photo');
        } catch (GuzzleException $e) {
            throw new ApiException('Network error: ' . $e->getMessage(), $e->getCode(), $e);
        }
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

    /**
     * @throws ApiException
     */
    private function handleRequestException(RequestException $e, string $context): void
    {
        $response = $e->hasResponse() ? $e->getResponse() : null;
        $statusCode = $response ? $response->getStatusCode() : 0;
        $responseBody = $response ? json_decode($response->getBody()->getContents(), true) : null;

        throw new ApiException(
            $context . ': ' . ($responseBody['message'] ?? $e->getMessage()),
            $statusCode,
            $e,
            $responseBody
        );
    }
}
