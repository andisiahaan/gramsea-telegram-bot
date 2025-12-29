<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Result object untuk mass message sending.
 * Tracks sent, blocked, dan failed chat IDs.
 */
class MassSendResult
{
    protected array $sent = [];
    protected array $blocked = [];
    protected array $failed = [];

    /**
     * Add successful send.
     */
    public function addSuccess(int|string $chatId): static
    {
        $this->sent[] = $chatId;
        return $this;
    }

    /**
     * Add blocked (403/400 error).
     */
    public function addBlocked(int|string $chatId): static
    {
        $this->blocked[] = $chatId;
        return $this;
    }

    /**
     * Add failed (other errors).
     */
    public function addFailed(int|string $chatId): static
    {
        $this->failed[] = $chatId;
        return $this;
    }

    /**
     * Get total successful sends.
     */
    public function totalSent(): int
    {
        return count($this->sent);
    }

    /**
     * Get total blocked.
     */
    public function totalBlocked(): int
    {
        return count($this->blocked);
    }

    /**
     * Get total failed.
     */
    public function totalFailed(): int
    {
        return count($this->failed);
    }

    /**
     * Get total processed.
     */
    public function totalProcessed(): int
    {
        return $this->totalSent() + $this->totalBlocked() + $this->totalFailed();
    }

    /**
     * Get sent chat IDs.
     */
    public function getSent(): array
    {
        return $this->sent;
    }

    /**
     * Get blocked chat IDs.
     */
    public function getBlocked(): array
    {
        return $this->blocked;
    }

    /**
     * Get failed chat IDs.
     */
    public function getFailed(): array
    {
        return $this->failed;
    }

    /**
     * Check if all sends were successful.
     */
    public function isAllSuccess(): bool
    {
        return $this->totalBlocked() === 0 && $this->totalFailed() === 0;
    }

    /**
     * Get success rate as percentage.
     */
    public function successRate(): float
    {
        $total = $this->totalProcessed();
        if ($total === 0) {
            return 0.0;
        }
        return round(($this->totalSent() / $total) * 100, 2);
    }
}
