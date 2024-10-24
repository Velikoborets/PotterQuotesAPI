<?php

namespace Classes;

header('Content-Type: application/json'); // Отдаём отвёт в JSON

class ApiService
{
    // Метод записи
    public function servicePost()
    {
        try {
            // Получаем данные из тела запроса и формируем строку
            $input = file_get_contents('php://input');

            // Проверяем размер данных
            if (strlen($input) > 10 * 1024 * 1024) {

                // логируем при большом запросе
                $this->logLargeRequest($input);
                throw new \Exception("Размер данных слишком велик!");

            } else {
                $quote = json_decode($input, true);
                $readyQuote = $quote['quote'];

                // Валидируем данные полученной строки и при успешной валидации, пишем в лог-файл и сохраняем в txt (без перезаписи)
                $this->validationStr($readyQuote);
            }
        } catch (\Exception $error) {
            echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    // Логируем большой запрос
    public function logLargeRequest($input)
    {
        $getIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $logData = 'Большой запрос от ' . $getIP . ' (' . date('Y-m-d H:i:s') . ')' . PHP_EOL;
        $logData .= 'Размер запроса: ' . strlen($input) . ' байт' . PHP_EOL;
        file_put_contents('../../logs/big-log.txt', $logData, FILE_APPEND);
    }

    // Сохраняет логирование всех изменений в отдельный лог-файл
    public function logAllChanges($readyQuote)
    {
        $getIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $getDate = date('Y-m-d H:i:s');
        $getLengthText = strlen($readyQuote);

        $prepareToRecord = 'IP клиента: ' . $getIP . PHP_EOL . 'Дата: ' . $getDate . PHP_EOL . 'Длина текста: ' . $getLengthText . PHP_EOL;
        $saveData = file_put_contents('../../logs/log.txt', $prepareToRecord, FILE_APPEND);
    }

    // Валидируем данные полученной строки:
    public function validationStr($readyQuote)
    {
        if (isset($readyQuote)) {
            $this->saveStr($readyQuote);
        } else {
            throw new \Exception("Строки для записи в файл - не сущесвует!");
        }
    }

    // Сохраняем строку в txt без перезаписи + логируем
    public function saveStr($readyQuote)
    {
        if (strlen($readyQuote) < 1000) {
            file_put_contents('../../misc/file.txt', $readyQuote . PHP_EOL, FILE_APPEND);
            $this->logAllChanges($readyQuote);
        } else {
            throw new \Exception("Длина строки больше 1000 символов!");
        }
    }

    // Метод чтения
    public function serviceGet() {
        try {
            $filePath = '../../misc/file.txt';
            // Проверяем IP и существование файла
            if ($this->checkIP() && $this->checkFile($filePath)) {
                // Чтение файла
                $fileContent = file($filePath, FILE_IGNORE_NEW_LINES);
                $totalLines = count($fileContent);

                // Проверка, что массив не пуст
                if ($totalLines > 0) {
                    // Возвращаем одну случайную цитату
                    $randomIndex = rand(0, $totalLines - 1);
                    $randomQuote = $fileContent[$randomIndex];
                    echo json_encode(['content' => $randomQuote], JSON_UNESCAPED_UNICODE);
                } else {
                    throw new \Exception("Файл пуст!");
                }
            }
        } catch (\Exception $error) {
            echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    // Проверка файла + обработка ошибок
    public function checkFile($filePath)
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            throw new \Exception("Файл не существует! Код ответа: " . http_response_code());
        }
        return true;
    }

    // Проверка IP + обработка ошибок
    public function checkIP()
    {
        $getIP = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $allowedIPs = ['127.0.0.1', '::1', 'localhost'];

        if (!in_array($getIP, $allowedIPs)) {
            http_response_code(403);
            throw new \Exception("Доступ запрещен! Код ответа: " . http_response_code());
        }
        return true;
    }

}

// Определяем метод запроса и вызываем нужный метод
$apiService = new ApiService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiService->servicePost();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $apiService->serviceGet();
}