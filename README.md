# Container API Client

PHP-клиент для работы с REST API управления контейнерами

## Оглавление

1. [Установка](#установка)
2. [Настройка](#настройка)
3. [Использование](#использование)
    - [Работа с контейнерами](#работа-с-контейнерами)
    - [Загрузка фотографий](#загрузка-фотографий)
    - [Справочники](#справочники)
4. [Обработка ошибок](#обработка-ошибок)
5. [Разработка](#разработка)
6. [Лицензия](#лицензия)

## Установка

```bash
composer require winolog/container-api-client
```

## Настройка

Создайте `.env` файл:

```ini
CONTAINER_API_BASE_URL="https://api.container-service.com"
CONTAINER_API_LOGIN="your_login"
CONTAINER_API_PASSWORD="your_password"
```

## Использование

### Инициализация

```php
use winolog\ContainerApiClient\ContainerApiClient;

$client = new ContainerApiClient();
```

### Работа с контейнерами

**Создание:**
```php
use winolog\ContainerApiClient\Dto\ContainerDto;

$container = new ContainerDto();
$container->container = 'TGHU1234567';
$container->container_year = 2023;
// ... другие поля

$response = $client->containers()->createContainer($container);
```

### Справочники

Доступные справочники:

| Метод клиента                     | API Endpoint                   | Описание                  |
|------------------------------------|--------------------------------|---------------------------|
| `getTerminals()`                  | `/terminals`                  | Терминалы                 |
| `getContainerSizes()`             | `/container-sizes`            | Размеры контейнеров       |
| `getContainerTypes()`             | `/container-types`            | Типы контейнеров          |
| `getCoolers()`                    | `/coolers`                    | Холодильные установки     |
| `getCoolerModels()`               | `/cooler-models`              | Модели холодильников      |
| `getCurrencies()`                 | `/currencies`                 | Валюты                    |
| `getSpecials()`                   | `/specials`                   | Специальные параметры     |
| `getContainerQualities()`         | `/container-qualities`        | Стандарты качества        |

**Примеры запросов:**

```php
// Получить все терминалы
$terminals = $client->references()->getTerminals();

// Получить типы контейнеров
$containerTypes = $client->references()->getContainerTypes();

// Получить модели холодильников
$coolerModels = $client->references()->getCoolerModels();
```

### Загрузка фотографий

```php
$response = $client->containers()->uploadContainerPhoto(
    123, // ID контейнера
    '/path/to/photo.jpg',
    'Описание фото' // необязательно
);
```

## Обработка ошибок

```php
try {
    $client->containers()->createContainer($container);
} catch (\winolog\ContainerApiClient\Exception\ApiException $e) {
    echo "Ошибка {$e->getCode()}: {$e->getMessage()}";
    if ($e->getResponseData()) {
        print_r($e->getResponseData());
    }
}
```

## Разработка

1. Установите зависимости:
```bash
composer install
```

2. Запустите тесты:
```bash
composer test
```

## Лицензия

MIT