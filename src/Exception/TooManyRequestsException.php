<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 429 Too Many Requests.
 * Terjadi saat rate limit tercapai.
 */
class TooManyRequestsException extends ApiException
{
    /**
     * {@inheritdoc}
     */
    public function isRetryable(): bool
    {
        return true;
    }

    /**
     * Get recommended wait time in seconds.
     * Alias untuk getRetryAfter() dengan default value.
     */
    public function getWaitTime(): int
    {
        return $this->getRetryAfter() ?? 30;
    }
}
