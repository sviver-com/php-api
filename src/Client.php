<?php

declare(strict_types = 1);

namespace Sviver\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Sviver\Api\Exception\ApiException;
use Throwable;

final class Client
{

    public static function build(string $apiKey, GuzzleClient $guzzle = null): self
    {
        return new self($apiKey, $guzzle ?? new GuzzleClient(), 'https://sviver.com/');
    }

    public static function buildTest(string $testApiKey, GuzzleClient $guzzle = null): self
    {
        return new self($testApiKey, $guzzle ?? new GuzzleClient(), 'https://beta.sviver.com/');
    }

    public function __construct(
        private readonly string $apiKey,
        private readonly GuzzleClient $guzzle,
        private readonly string $baseUri,
    ) {
    }

    /** @throws ApiException */
    public function postJson(string $path, array $data = []): Response
    {
        try {
            return $this->guzzle->post($path, $this->buildOptions($data));
        } catch (Throwable $throwable) {
            $response = null;
            if ($throwable instanceof RequestException) {
                $response = $throwable->getResponse();
            }

            throw new ApiException($throwable->getMessage(), $throwable->getCode(), $throwable, $response);
        }
    }

    private function buildOptions(array $data): array
    {
        return [
            'base_uri'              => $this->baseUri,
            RequestOptions::HEADERS => [
                'Authorization' => $this->apiKey,
            ],
            RequestOptions::JSON    => $data,
        ];
    }
}