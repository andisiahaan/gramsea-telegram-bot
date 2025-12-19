<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Exception untuk HTTP 400 Bad Request.
 * Terjadi saat parameter yang dikirim tidak valid atau salah format.
 */
class BadRequestException extends ApiException
{
}
