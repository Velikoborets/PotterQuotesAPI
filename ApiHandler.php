<?php

require_once 'CurlHandler.php';

class ApiHandler
{

    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function sendPostRequest($data)
    {
        $response = CurlHandler::post($this->url, $data);
        $jsonResponse = json_decode($response, true);

        if (isset($jsonResponse['error'])) {
            echo $jsonResponse['error'];
        }

        return $jsonResponse;
    }

    public function sendGetRequest()
    {
        $response = CurlHandler::get($this->url);
        $jsonResponse = json_decode($response, true);

        if (isset($jsonResponse['content'])) {
            echo $jsonResponse['content'];
        }

        if (isset($jsonResponse['error'])) {
            echo $jsonResponse['error'];
        }

        return $jsonResponse;
    }
}

$url = 'http://arturvelikoborets-testtask/REST-API/ApiService.php?limit=20';
$randomStr = bin2hex(random_bytes(8));
$data = json_encode(['randomStr' => $randomStr]);

$apiHandler = new ApiHandler($url);

// Отправка POST-запроса + ответ
$resPOST = $apiHandler->sendPostRequest($data);

// Отправка GET-запроса
$resGET = $apiHandler->sendGetRequest();