<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Support\InlineKeyboard;

/**
 * Abstract base class untuk semua sender classes.
 * 
 * Menyediakan properties dan methods umum untuk TextSender, MediaSender,
 * MediaGroupSender, dan MessageSender.
 */
abstract class BaseSender
{
    protected Gramsea $bot;
    protected int|string $chatId = '';
    protected ?array $replyMarkup = null;
    protected string $parseMode = 'HTML';
    protected bool $disableNotification = false;
    protected bool $protectContent = false;
    protected ?int $replyToMessageId = null;
    protected bool $allowPaidBroadcast = false;

    public function __construct(Gramsea|string $bot)
    {
        if (is_string($bot)) {
            $this->bot = new Gramsea($bot);
        } else {
            $this->bot = $bot;
        }
    }

    /**
     * Create new instance.
     */
    public static function make(Gramsea|string $bot): static
    {
        return new static($bot);
    }

    /**
     * Alias untuk make().
     */
    public static function bot(Gramsea|string $bot): static
    {
        return static::make($bot);
    }

    /**
     * Set target chat ID.
     */
    public function to(int|string $chatId): static
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * Alias untuk to().
     */
    public function chat(int|string $chatId): static
    {
        return $this->to($chatId);
    }

    /**
     * Set keyboard (inline keyboard, reply keyboard, dll).
     */
    public function keyboard(InlineKeyboard|array|string|null $markup): static
    {
        if ($markup === null) {
            $this->replyMarkup = null;
            return $this;
        }

        if ($markup instanceof InlineKeyboard) {
            $this->replyMarkup = $markup->toArray();
            return $this;
        }

        if (is_string($markup)) {
            $decoded = json_decode($markup, true);
            $this->replyMarkup = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            return $this;
        }

        $this->replyMarkup = $markup;
        return $this;
    }

    /**
     * Alias untuk keyboard().
     */
    public function replyMarkup(InlineKeyboard|array|string|null $markup): static
    {
        return $this->keyboard($markup);
    }

    /**
     * Set parse mode (HTML, Markdown, MarkdownV2).
     */
    public function parseMode(string $mode): static
    {
        $this->parseMode = $mode;
        return $this;
    }

    /**
     * Disable notification (silent message).
     */
    public function silent(bool $silent = true): static
    {
        $this->disableNotification = $silent;
        return $this;
    }

    /**
     * Protect content from forwarding/saving.
     */
    public function protect(bool $protect = true): static
    {
        $this->protectContent = $protect;
        return $this;
    }

    /**
     * Reply to specific message.
     */
    public function replyTo(int $messageId): static
    {
        $this->replyToMessageId = $messageId;
        return $this;
    }
    
    public function allowPaidBroadcast(bool $allow = true): static
    {
        $this->allowPaidBroadcast = $allow;
        return $this;
    }

    /**
     * Get underlying Gramsea bot instance.
     */
    public function getBot(): Gramsea
    {
        return $this->bot;
    }

    /**
     * Apply common options to params array.
     */
    protected function applyCommonParams(array &$params): void
    {
        if ($this->replyMarkup) {
            $params['reply_markup'] = json_encode($this->replyMarkup);
        }

        if ($this->disableNotification) {
            $params['disable_notification'] = true;
        }

        if ($this->protectContent) {
            $params['protect_content'] = true;
        }

        if ($this->replyToMessageId !== null) {
            $params['reply_to_message_id'] = $this->replyToMessageId;
        }

        if ($this->allowPaidBroadcast) {
            $params['allow_paid_broadcast'] = true;
        }
    }

    /**
     * Reset common state.
     */
    protected function resetCommonState(): void
    {
        $this->chatId = '';
        $this->replyMarkup = null;
        $this->parseMode = 'HTML';
        $this->disableNotification = false;
        $this->protectContent = false;
        $this->replyToMessageId = null;
        $this->allowPaidBroadcast = false;
    }

    /**
     * Validate chat ID is set.
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateChatId(): void
    {
        if (empty($this->chatId)) {
            throw new \InvalidArgumentException('Chat ID is required. Use ->to($chatId) method.');
        }
    }

    /**
     * Execute dan kirim pesan. Harus diimplementasikan oleh child class.
     */
    abstract public function send(): array;

    /**
     * Reset state untuk reuse instance. Harus diimplementasikan oleh child class.
     */
    abstract public function reset(): static;
}
