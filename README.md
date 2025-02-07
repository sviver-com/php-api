# Sviver api

# Как работать

1. Создаем `Client`, на основе `ApiKey`
2. Создаем `Api`, на основе `Client`
3. Пользуемся. Например, отправляем события.

### Примеры кода

**Production**

```php
$apiKey = 'some-production-key'; // Получить нужно у менеджера siviver
$client = \Sviver\Api\Client::build($apiKey, new \GuzzleHttp\Client());
$api = new \Sviver\Api\Api($client);

// Или можно просто
$api = new \Sviver\Api\Api::build($apiKey);

$api->sendEvent('new_order', 999999999999, [
    'order_id' => 1,
    'order_timestamp' => 9999999999,
    'price' => 9999999999,
    'products' => [
        [
            'id' => 1,
            'name' => 'Диван красный',
            'photos' => [
                ['url' => 'https://example.com/photo1.jpg'],
                ['url' => 'https://example.com/photo2.jpg'],
            ],
        ]
    ]
]);
```

---

**Тестовая среда**

Тут все то же самое, только клиент нужно создавать через статический метод `buildTest`

```php
$apiKey = 'some-test-key'; // Получить нужно у менеджера siviver
$client = \Sviver\Api\Client::buildTest($apiKey, new \GuzzleHttp\Client());
$api = new \Sviver\Api\Api($client);

$api->sendEvent('new_order', 999999999999, [
    'order_id' => 1,
    'order_timestamp' => 9999999999,
    'price' => 9999999999,
    'products' => [
        [
            'id' => 1,
            'name' => 'Диван красный',
            'photos' => [
                ['url' => 'https://example.com/photo1.jpg'],
                ['url' => 'https://example.com/photo2.jpg'],
            ],
        ]
    ]
]);
```