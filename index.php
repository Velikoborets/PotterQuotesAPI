<?php

$url = 'http://taskvelikoborets/task2/api.php'; // API, которое работает с рандомной строкой

$randomStr = bin2hex(random_bytes(8));
$randomStr = json_encode(['randomStr' => $randomStr]); // формируем рандомную строку для отправки к API


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// POST запрос
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $randomStr);
$resPOST = curl_exec($curl);

// GET запрос + вывод содержимого
curl_setopt($curl, CURLOPT_POST, 0); // Сбрасываем POST
$resGET = curl_exec($curl);
$jsonResponseGET = json_decode($resGET, true);
echo isset($jsonResponseGET['content']) ? $jsonResponseGET['content'] : '';

// Вывод ошибок валидации с API - GET
$jsonResponseGET = json_decode($resGET, true);
echo isset($jsonResponseGET['error']) ? $jsonResponseGET['error'] : '';

// Вывод ошибок валидации POST
$jsonResponsePOST = json_decode($resPOST, true);
echo isset($jsonResponsePOST['error']) ? $jsonResponsePOST['error'] : '';

curl_close($curl); // Закрываем соединение