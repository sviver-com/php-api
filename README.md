# Sviver api

## Установка

```shell
composer require sviver/api
```

# Как работать

1. Создаем `\Sviver\Api\Client`, на основе `$apiKey`
2. Создаем `\Sviver\Api\Api`, на основе `\Sviver\Api\Client`
3. Пользуемся. Например, отправляем события.

### Примеры кода

**Production**

```php
$apiKey = 'some-production-key'; // Получить нужно у менеджера siviver
$client = \Sviver\Api\Client::build($apiKey, new \GuzzleHttp\Client());
$api = new \Sviver\Api\Api($client);

// Или можно просто
$api = \Sviver\Api\Api::build($apiKey);

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

### Валидация GET параметров после успешной аутентификации

При аутентификации через login-widget, будет редирект на ваш сайт.
GET параметрами будут переданы поля:

| Поле           | Тип данных | Обязательный | Комментарий                                                                                  |
|----------------|------------|---------------|----------------------------------------------------------------------------------------------|
| **id**         | `int`      | ✅           | Уникальный идентификатор пользователя                                                        |
| **auth_date**  | `int`      | ✅           | Время аутентификации пользователя. timestamp                                                 |
| **first_name** | `string`   | ✅           | Имя пользователя                                                                             |
| **last_name**  | `string`   | ❌           | Фамилия пользователя (может отсутствовать)                                                   |
| **username**   | `string`   | ❌           | никнейм пользователя (никнейм, может отсутствовать)                                          |
| **photo_url**  | `string`   | ❌           | Ссылка на фото пользователя (может отсутствовать)                                            |
| **phone**      | `string`   | ❌/✅       | Номер телефона Telegram пользователя в международном формате. Обязателен по требованию сайта |

Этими полями можно воспользоваться, но предварительно нужно явно проверить, что они пришли из sviver, а не зловреда.
Для этого можно просто вызвать проверку

```php
\Sviver\Api\Client::build($apiKey)->isValidAuthParams($_GET); // Вернет boolean
```