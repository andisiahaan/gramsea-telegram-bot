<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 404 Not Found.
 * Terjadi saat chat/user/message tidak ditemukan.
 */
class NotFoundException extends ApiException
{
    /**
     * Check apakah chat tidak ditemukan.
     */
    public function isChatNotFound(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'chat not found');
    }

    /**
     * Check apakah message tidak ditemukan.
     */
    public function isMessageNotFound(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'message to');
    }
}
