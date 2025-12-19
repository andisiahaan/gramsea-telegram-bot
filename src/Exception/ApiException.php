<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

/**
 * Base exception untuk semua Telegram API errors.
 */
class ApiException extends \RuntimeException
{
    protected array $response;
    protected int $errorCode;

    public function __construct(string $message, array $response = [], int $errorCode = 0)
    {
        parent::__construct($message, $errorCode);
        $this->response = $response;
        $this->errorCode = $errorCode;
    }

    /**
     * Get full response array dari Telegram.
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Get HTTP/Telegram error code (400, 403, 429, dll).
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Get description dari Telegram response.
     */
    public function getDescription(): string
    {
        return $this->response['description'] ?? $this->getMessage();
    }

    /**
     * Get retry_after value (untuk rate limiting).
     * Override di TooManyRequestsException.
     */
    public function getRetryAfter(): ?int
    {
        $params = $this->response['parameters'] ?? [];
        return isset($params['retry_after']) ? (int) $params['retry_after'] : null;
    }

    /**
     * Get migrate_to_chat_id (untuk supergroup migration).
     */
    public function getMigrateToChatId(): ?int
    {
        $params = $this->response['parameters'] ?? [];
        return isset($params['migrate_to_chat_id']) ? (int) $params['migrate_to_chat_id'] : null;
    }

    /**
     * Check apakah error bisa di-retry.
     */
    public function isRetryable(): bool
    {
        return $this->getRetryAfter() !== null || $this->errorCode >= 500;
    }
}
