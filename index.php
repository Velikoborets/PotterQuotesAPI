<?php

require_once __DIR__ . '/vendor/autoload.php';

// Включаем необходимые файлы
use Classes\Quote;
use Classes\ApiHandler;

// Стили
echo "<link rel=\"stylesheet\" href=\"public/style/style.css\">";

// Получаем метод запроса
$requestMethod = $_SERVER['REQUEST_METHOD'];

$url = 'http://github-projects/PotterQuotesAPI/src/classes/ApiService.php?limit=20';
$quote = Quote::getQuote();
$data = json_encode(['quote' => $quote]);

$apiHandler = new ApiHandler($url);

// Отправка POST-запроса + ответ
$resPOST = $apiHandler->sendPostRequest($data);

// Отправка GET-запроса + ответ
echo "<div class=\"container\">";
echo '<h3>Цитаты из Гарри Поттера</h3>';
$resGET = $apiHandler->sendGetRequest();
echo "</div>";