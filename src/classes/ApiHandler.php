<?php

namespace Classes;

use Classes\CurlHandler;

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