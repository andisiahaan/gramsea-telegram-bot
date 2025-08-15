<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\NetworkException;

class Curl
{
    public static function request(string $url, array $parameters = [], string $method = 'GET'): array
    {
        $ch = curl_init();

        $headers = [];

        // Detect if there's a file-like value for multipart
        $isMultipart = false;
        foreach ($parameters as $k => $v) {
            if (is_string($v) && (str_starts_with($v, '@') || file_exists((string) $v))) {
                $isMultipart = true;
                break;
            }
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($method === 'GET' && count($parameters) > 0) {
            $options[CURLOPT_URL] = $url . '?' . http_build_query($parameters);
        } else {
            $options[CURLOPT_URL] = $url;
            $options[CURLOPT_POST] = true;

            if ($isMultipart) {
                foreach ($parameters as $k => $v) {
                    if (is_string($v) && file_exists($v)) {
                        $parameters[$k] = new \CURLFile($v);
                    }
                }

                $options[CURLOPT_POSTFIELDS] = $parameters;
            } else {
                $headers[] = 'Content-Type: application/json';
                $options[CURLOPT_POSTFIELDS] = json_encode($parameters);
            }
        }

        if (!empty($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new NetworkException('Curl error: ' . $error);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // return raw fallback as array
        return ['ok' => false, 'raw' => $response, 'http_code' => $httpCode];
    }
}
