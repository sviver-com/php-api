<?php

declare(strict_types = 1);

namespace Sviver\Api;

use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use Sviver\Api\Exception\ApiException;

final class Api
{

    public static function build(string $apiKey): self
    {
        return new self(Client::build($apiKey));
    }

    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * Отправка события в sviver
     *
     * @param string $eventType      Тип события. Может быть любой строкой. Прим.: 'new_order'
     * @param int    $telegramUserId Идентификатор пользователя в telegram.
     * @param array  $data           Данные события. Эти данные будут сериализованы в json.
     * @return string UUID события
     * @throws ApiException
     */
    public function sendEvent(string $eventType, int $telegramUserId, array $data = []): string
    {
        $response = $this->client->postJson('/app/api/ev/event/create', [
            'type'           => $eventType,
            'telegramUserId' => $telegramUserId,
            'data'           => $data,
        ]);

        return $this->unwrap($response);
    }

    /**
     * Проверяет на валидность полученных данных после редиректа.
     * Можно передать $_GET или его аналог.
     *
     * @param array $params
     * @return bool
     */
    public function isValidAuthParams(array $params): bool
    {
        return $this->client->isValidAuthParams($params);
    }

    /** @throws ApiException */
    private function unwrap(Response $response): mixed
    {
        $contents = $response->getBody()->getContents();
        try {
            $data = Utils::jsonDecode($contents, true);
        } catch (InvalidArgumentException $exception) {
            throw new ApiException($exception->getMessage(), $exception->getCode(), $exception, $response);
        }

        if (!isset($data['isOk'])) {
            throw new ApiException('Unexpected Api response: "isOk" expected', response: $response);
        }

        if ($data['isOk'] === true) {
            return $data['data'];
        }

        if ($data['isOk'] === false) {
            throw new ApiException($data['error']['message'], response: $response);
        }

        throw new ApiException('Unexpected Api response', response: $response);
    }
}