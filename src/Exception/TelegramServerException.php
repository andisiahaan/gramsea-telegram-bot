<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 500+ Server Error.
 * Terjadi saat Telegram server bermasalah.
 */
class TelegramServerException extends ApiException
{
    /**
     * {@inheritdoc}
     */
    public function isRetryable(): bool
    {
        return true;
    }
}
