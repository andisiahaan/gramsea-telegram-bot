<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 401 Unauthorized.
 * Terjadi saat bot token tidak valid atau expired.
 */
class UnauthorizedException extends ApiException
{
}
