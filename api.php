<?php

header('Content-Type: application/json'); // Отдаём отвёт в JSON

// МЕТОД ЗАПИСИ
try {

    // Принимает POST-запрос с данными (например, JSON).
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Получаем данные из тела запроса и формируем строку
        $input = file_get_contents('php://input');
        $randomStr = json_decode($input, true);
        $readyStr = $randomStr['randomStr'];

        if (isset($readyStr)) {

            // Валидирует данные: проверьте, что длина строки не превышает 1000 символов.
            if (strlen($readyStr) < 1000) {

                // Сохраняет данные в текстовый файл, но каждый новый POST-запрос добавляет данные в файл, не перезаписывая его.
                file_put_contents('file.txt', $readyStr . PHP_EOL, FILE_APPEND);

                // Сохраняет логирование всех изменений в отдельный лог-файл (пишем дату, IP-адрес и длину добавленного текста).
                $getIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
                $getDate = date('Y-m-d H:i:s');
                $getLengthText = strlen($readyStr);

                $prepareToRecord = 'IP клиента: ' . $getIP . PHP_EOL . 'Дата: ' . $getDate . PHP_EOL . 'Длина текста: ' . $getLengthText . PHP_EOL;
                $saveData = file_put_contents('log.txt', $prepareToRecord, FILE_APPEND);

            } else {
                throw new \Exception("Длина строки больше 1000 символов!");
            }

        } else {
            throw new \Exception("Строки для записи в файл - не сущесвует!");
        }
    }

} catch (\Exception $error) {
    echo json_encode(['error' => $error->getMessage()],JSON_UNESCAPED_UNICODE);
}


// МЕТОД ЧТЕНИЯ (Обрабатывает GET-запросы для чтения данных из файла)
try {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // Получаем IP (аналогично POST)
        $getIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

        // "Виды" локальных IP
        $allowedIPs = ['127.0.0.1', '::1', 'localhost'];

        // Проверка IP
        if (!in_array($getIP, $allowedIPs)) {
            http_response_code(403); // Доступ запрещен
            throw new \Exception("Доступ запрещен!");
        }

        // Проверка Файла
        if (!file_exists('file.txt')) {
            http_response_code(404); // Файл не найден
            throw new \Exception("Файл не существует! Код ответа: " . http_response_code());
        } else {
            $recordFile = file_get_contents('file.txt');
            echo json_encode(['content' => $recordFile]);
        }
    }

} catch (\Exception $error) {
    echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
}