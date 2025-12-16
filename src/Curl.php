<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\NetworkException;

class Curl
{
    /**
     * Default timeout in seconds.
     */
    protected static int $timeout = 30;

    /**
     * Default connection timeout in seconds.
     */
    protected static int $connectTimeout = 10;

    /**
     * Number of retry attempts.
     */
    protected static int $retryAttempts = 0;

    /**
     * Delay between retries in milliseconds.
     */
    protected static int $retryDelay = 500;

    /**
     * Set default timeout.
     */
    public static function setTimeout(int $seconds): void
    {
        self::$timeout = $seconds;
    }

    /**
     * Set connection timeout.
     */
    public static function setConnectTimeout(int $seconds): void
    {
        self::$connectTimeout = $seconds;
    }

    /**
     * Set retry configuration.
     * 
     * @param int $attempts Number of retry attempts (0 = no retry)
     * @param int $delayMs Delay between retries in milliseconds
     */
    public static function setRetry(int $attempts, int $delayMs = 500): void
    {
        self::$retryAttempts = $attempts;
        self::$retryDelay = $delayMs;
    }

    /**
     * Make HTTP request.
     */
    public static function request(string $url, array $parameters = [], string $method = 'GET'): array
    {
        $lastException = null;
        $attempts = self::$retryAttempts + 1;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                return self::doRequest($url, $parameters, $method);
            } catch (NetworkException $e) {
                $lastException = $e;
                
                // Don't retry on last attempt
                if ($attempt < $attempts) {
                    usleep(self::$retryDelay * 1000);
                }
            }
        }

        throw $lastException;
    }

    /**
     * Perform the actual HTTP request.
     */
    protected static function doRequest(string $url, array $parameters, string $method): array
    {
        $ch = curl_init();

        $headers = [];

        // Detect if there's a file-like value for multipart
        $isMultipart = self::hasFileUpload($parameters);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::$timeout,
            CURLOPT_CONNECTTIMEOUT => self::$connectTimeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if ($method === 'GET' && count($parameters) > 0) {
            $options[CURLOPT_URL] = $url . '?' . http_build_query($parameters);
        } else {
            $options[CURLOPT_URL] = $url;
            $options[CURLOPT_POST] = true;

            if ($isMultipart) {
                $options[CURLOPT_POSTFIELDS] = self::prepareMultipart($parameters);
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
        $errno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        if ($response === false) {
            throw new NetworkException("Curl error ({$errno}): {$error}", $errno);
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $decoded['http_code'] = $httpCode;
            return $decoded;
        }

        // Return raw fallback as array
        return [
            'ok' => false,
            'raw' => $response,
            'http_code' => $httpCode
        ];
    }

    /**
     * Check if parameters contain file upload.
     */
    protected static function hasFileUpload(array $parameters): bool
    {
        foreach ($parameters as $value) {
            if ($value instanceof \CURLFile) {
                return true;
            }
            if (is_string($value) && file_exists($value) && is_file($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare parameters for multipart upload.
     */
    protected static function prepareMultipart(array $parameters): array
    {
        foreach ($parameters as $key => $value) {
            if (is_string($value) && file_exists($value) && is_file($value)) {
                $parameters[$key] = new \CURLFile($value);
            }
        }
        return $parameters;
    }

    /**
     * Simple GET request (for non-API calls).
     */
    public static function get(string $url, array $query = []): ?string
    {
        $ch = curl_init();

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::$timeout,
            CURLOPT_CONNECTTIMEOUT => self::$connectTimeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response !== false ? $response : null;
    }

    /**
     * Download file to path.
     */
    public static function download(string $url, string $savePath): bool
    {
        $ch = curl_init($url);
        $fp = fopen($savePath, 'w');

        if ($fp === false) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_TIMEOUT => self::$timeout * 2, // Double timeout for downloads
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
        ]);

        $success = curl_exec($ch);
        
        curl_close($ch);
        fclose($fp);

        if ($success === false) {
            @unlink($savePath);
            return false;
        }

        return true;
    }
}
