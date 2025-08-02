<?php
require_once __DIR__.'/../vendor/autoload.php';

use winolog\ContainerApiClient\ContainerApiClient;
use winolog\ContainerApiClient\Dto\ContainerDto;
use winolog\ContainerApiClient\Exception\ApiException;

// Инициализация клиента
$client = new ContainerApiClient();

try {
    echo "=== Тестирование справочников ===\n";

    // 1. Проверка справочников
    $references = [
        'terminals' => $client->references()->getTerminals(),
        'container_sizes' => $client->references()->getContainerSizes(),
        'container_types' => $client->references()->getContainerTypes(),
        'cooler_models' => $client->references()->getCoolerModels(),
        'special' => $client->references()->getSpecials(),
        'container_quality' => $client->references()->getContainerQualities(),
    ];

    foreach ($references as $name => $data) {
        echo "Справочник {$name}: " . count($data['data']['items']) . " записей\n";
        if (!empty($data)) {
            echo "Первая запись: " . json_encode($data['data']['items'], JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }

    echo "\n=== Тестирование создания контейнера ===\n";

    // 2. Тест создания контейнера
    $containerDto = new ContainerDto();
    $containerDto->container = 'TEST' . mt_rand(100000, 999999);
    $containerDto->container_year = 2023;
    $containerDto->type = $references['container_types']['data']['items'][0]['id'] ?? 1;
    $containerDto->size = $references['container_sizes']['data']['items'][0]['id'] ?? 1;
    $containerDto->terminal_id = $references['terminals']['data']['items'][0]['id'] ?? 1;

    // Заполните остальные обязательные поля по аналогии
    $containerDto->cooler = 1;
    $containerDto->cooler_model = $references['cooler_models']['data']['items'][0]['id'] ?? 1;
    $containerDto->temp_admission = -18;
    $containerDto->capacity = 30000;
    $containerDto->tare = 4000;
    $containerDto->price = 150000;
    $containerDto->cooler_year = 2004;
    $containerDto->special = $references['special']['data']['items'][0]['id'] ?? 1;
    $containerDto->container_quality_id = $references['container_quality']['data']['items'][0]['id'] ?? 1;

    $createdContainer = $client->containers()->createContainer($containerDto);
    echo "Контейнер создан: " . json_encode($createdContainer, JSON_UNESCAPED_UNICODE) . "\n";
    $containerId = $createdContainer['data']['id'] ?? null;

    if ($containerId) {
        echo "\n=== Тестирование загрузки фото ===\n";
        // 3. Тест загрузки фото (используйте реальный путь к файлу)
        $testPhotoPath = __DIR__.'/test.png';

        if (file_exists($testPhotoPath)) {
            $photoResponse = $client->containers()->uploadContainerPhoto(
                $containerId,
                $testPhotoPath,
                'Тестовое фото контейнера'
            );
            echo "Фото загружено: " . json_encode($photoResponse, JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "Тестовое фото не найдено по пути: {$testPhotoPath}\n";
            echo "Создайте файл или укажите путь к существующему JPEG файлу\n";
        }
    }

} catch (ApiException $e) {
    echo "\nОшибка API:\n";
    echo "Сообщение: " . $e->getMessage() . "\n";
    echo "Код: " . $e->getCode() . "\n";

    if ($e->getResponseData()) {
        echo "Данные ответа:\n";
        print_r($e->getResponseData());
    }
} catch (\Exception $e) {
    echo "\nОбщая ошибка:\n";
    echo get_class($e) . ": " . $e->getMessage() . "\n";
}

echo "\nТестирование завершено\n";
