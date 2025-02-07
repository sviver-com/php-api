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

    public const AUTH_FIELDS = [
        'id',
        'hash',
        'auth_date',
        'first_name',
        'last_name',
        'username',
        'photo_url',
    ];

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

    public function isValidAuthParams(array $params): bool
    {
        $params = $this->extractNecessaryFields($params);
        if (!isset($params['hash'])) {
            return false;
        }

        $hash = $params['hash'];
        unset($params['hash']);

        return (strcmp($hash, $this->generateHashFromParams($params)) === 0);
    }

    public function generateHashFromParams(array $params): string
    {
        $dataCheckArray = [];
        foreach ($params as $key => $value) {
            $dataCheckArray[] = $key . '=' . $value;
        }

        sort($dataCheckArray);
        $checkString = implode("\n", $dataCheckArray);

        return hash_hmac(
            'sha256',
            $checkString,
            hash(
                'sha256',
                $this->apiKey,
                true,
            ),
        );
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

    private function extractNecessaryFields(array $params): array
    {
        return array_filter(
            $params,
            fn($key) => in_array($key, self::AUTH_FIELDS, true),
            ARRAY_FILTER_USE_KEY,
        );
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