<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk network/connection errors.
 */
class NetworkException extends \RuntimeException
{
    protected ?int $curlErrorCode = null;

    public function __construct(string $message, int $curlErrorCode = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->curlErrorCode = $curlErrorCode > 0 ? $curlErrorCode : null;
    }

    /**
     * Get cURL error code.
     */
    public function getCurlErrorCode(): ?int
    {
        return $this->curlErrorCode;
    }

    /**
     * Check if it's a timeout error.
     */
    public function isTimeout(): bool
    {
        return in_array($this->curlErrorCode, [
            CURLE_OPERATION_TIMEDOUT,
            CURLE_OPERATION_TIMEOUTED ?? 28,
        ]);
    }

    /**
     * Check if it's a connection error.
     */
    public function isConnectionError(): bool
    {
        return in_array($this->curlErrorCode, [
            CURLE_COULDNT_CONNECT,
            CURLE_COULDNT_RESOLVE_HOST,
            CURLE_COULDNT_RESOLVE_PROXY,
        ]);
    }

    /**
     * Check if it's an SSL error.
     */
    public function isSslError(): bool
    {
        return in_array($this->curlErrorCode, [
            CURLE_SSL_CONNECT_ERROR,
            CURLE_SSL_CERTPROBLEM,
            CURLE_SSL_CIPHER,
            CURLE_SSL_CACERT,
        ]);
    }
}
