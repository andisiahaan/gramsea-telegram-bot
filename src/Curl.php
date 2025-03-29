<?php

namespace Andisiahaan\GramseaTelegramBot;

class Curl
{
    public static function request(string $url, array $parameters = [], string $method = 'GET'): array
    {
        // Tentukan batas karakter untuk key "text"
        $maxTextLength = 2000;

        // Jika key "text" ada dan panjangnya lebih dari batas, ubah metode ke POST
        if (isset($parameters['text']) && is_string($parameters['text']) && strlen($parameters['text']) > $maxTextLength) {
            $method = 'POST';
        }

        $ch = curl_init();

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($parameters);
            $options[CURLOPT_URL] = $url;
        } else { // GET
            $queryString = http_build_query($parameters);
            $options[CURLOPT_URL] = $url . '?' . $queryString;
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($response, true) ?? $response;
    }
}
