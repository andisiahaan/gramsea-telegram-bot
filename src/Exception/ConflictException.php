<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 409 Conflict.
 * Terjadi saat ada conflict webhook atau long polling.
 */
class ConflictException extends ApiException
{
    /**
     * Check apakah conflict karena webhook sudah diset.
     */
    public function isWebhookConflict(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'webhook');
    }

    /**
     * Check apakah conflict karena getUpdates aktif.
     */
    public function isGetUpdatesConflict(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'getupdates');
    }
}
