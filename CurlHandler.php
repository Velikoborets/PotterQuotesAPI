<?php

class CurlHandler
{
    public static function post($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);

        if ($response === false) {
            error_log('Ошибка запроса CURL: ' . curl_error($curl));
        }

        curl_close($curl);
        return $response;
    }

    public static function get($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);

        if ($response === false) {
            error_log('Ошибка запроса CURL: ' . curl_error($curl));
        }

        curl_close($curl);
        return $response;
    }
}