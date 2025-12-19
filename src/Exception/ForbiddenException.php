<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 403 Forbidden.
 * Terjadi saat bot tidak memiliki akses (diblok user, bukan member group, dll).
 */
class ForbiddenException extends ApiException
{
    /**
     * Check apakah bot diblok oleh user.
     */
    public function isBotBlocked(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'bot was blocked');
    }

    /**
     * Check apakah bot tidak punya hak kirim pesan.
     */
    public function hasNoRightsToSend(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'no rights to send');
    }

    /**
     * Check apakah bot di-kick dari group.
     */
    public function isBotKicked(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'bot was kicked');
    }

    /**
     * Check apakah user deactivated.
     */
    public function isUserDeactivated(): bool
    {
        return str_contains(strtolower($this->getMessage()), 'user is deactivated');
    }
}
