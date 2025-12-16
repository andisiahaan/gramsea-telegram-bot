<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;

/**
 * General fluent chaining sender yang otomatis memilih sender sesuai context.
 * 
 * Class ini memeriksa dan mengarahkan ke sender yang tepat:
 * - Text only → TextSender
 * - Text + 1 media → MediaSender
 * - Text + multiple media (2+) → MediaGroupSender
 * 
 * @example
 * MessageSender::bot($bot)
 *     ->to($chatId)
 *     ->text('Hello **world**!')
 *     ->photo('https://example.com/image.jpg')
 *     ->keyboard(InlineKeyboard::make()->callback('Click', 'action'))
 *     ->send();
 */
class MessageSender extends BaseSender
{
    protected ?string $text = null;
    protected array $media = [];

    /**
     * Alias untuk make().
     */
    public static function create(Gramsea|string $bot): static
    {
        return static::make($bot);
    }

    /**
     * Set text pesan. Mendukung markdown sederhana yang dikonversi ke HTML.
     */
    public function text(string $text): static
    {
        $this->text = TextFormatter::markdownToHtml($text);
        return $this;
    }

    /**
     * Set text pesan dengan HTML langsung tanpa konversi.
     */
    public function html(string $html): static
    {
        $this->text = $html;
        return $this;
    }

    // =========================================================================
    // CHAINING METHODS - Media
    // =========================================================================

    /**
     * Add photo.
     */
    public function photo(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add video.
     */
    public function video(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add audio.
     */
    public function audio(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add document.
     */
    public function document(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add animation (GIF).
     */
    public function animation(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add voice.
     */
    public function voice(string $url): static
    {
        $this->media[] = $url;
        return $this;
    }

    /**
     * Add media - single URL atau array URLs.
     */
    public function media(string|array $urls): static
    {
        $urls = is_array($urls) ? $urls : [$urls];
        foreach ($urls as $url) {
            $this->media[] = $url;
        }
        return $this;
    }

    // =========================================================================
    // SEND - Execute and delegate to appropriate sender
    // =========================================================================

    /**
     * Execute dan kirim pesan.
     * 
     * Method ini otomatis menentukan sender yang tepat:
     * - Text only → TextSender
     * - 1 media (with/without text) → MediaSender
     * - 2+ media (with/without text) → MediaGroupSender
     * 
     * @return array Telegram API response
     * @throws \InvalidArgumentException Jika parameter tidak valid
     * @throws ApiException Jika Telegram API mengembalikan error
     */
    public function send(): array
    {
        $this->validateChatId();

        if (empty($this->text) && empty($this->media)) {
            throw new \InvalidArgumentException('Either text or media is required.');
        }

        $mediaCount = count($this->media);

        // Text only → TextSender
        if (!empty($this->text) && $mediaCount === 0) {
            return $this->sendViaTextSender();
        }

        // 1 media (with/without caption) → MediaSender
        if ($mediaCount === 1) {
            return $this->sendViaMediaSender();
        }

        // 2+ media (with/without caption) → MediaGroupSender
        return $this->sendViaMediaGroupSender();
    }

    /**
     * Delegate ke TextSender.
     */
    protected function sendViaTextSender(): array
    {
        $sender = TextSender::make($this->bot)
            ->to($this->chatId)
            ->parseMode($this->parseMode);

        if ($this->text) {
            $sender->html($this->text);
        }

        if ($this->replyMarkup) {
            $sender->keyboard($this->replyMarkup);
        }

        if ($this->disableNotification) {
            $sender->silent();
        }

        if ($this->protectContent) {
            $sender->protect();
        }

        if ($this->replyToMessageId !== null) {
            $sender->replyTo($this->replyToMessageId);
        }

        return $sender->send();
    }

    /**
     * Delegate ke MediaSender.
     */
    protected function sendViaMediaSender(): array
    {
        $sender = MediaSender::make($this->bot)
            ->to($this->chatId)
            ->media($this->media[0])
            ->parseMode($this->parseMode);

        if ($this->text) {
            $sender->captionHtml($this->text);
        }

        if ($this->replyMarkup) {
            $sender->keyboard($this->replyMarkup);
        }

        if ($this->disableNotification) {
            $sender->silent();
        }

        if ($this->protectContent) {
            $sender->protect();
        }

        if ($this->replyToMessageId !== null) {
            $sender->replyTo($this->replyToMessageId);
        }

        return $sender->send();
    }

    /**
     * Delegate ke MediaGroupSender.
     */
    protected function sendViaMediaGroupSender(): array
    {
        $sender = MediaGroupSender::make($this->bot)
            ->to($this->chatId)
            ->media($this->media)
            ->parseMode($this->parseMode);

        if ($this->text) {
            $sender->captionHtml($this->text);
        }

        if ($this->replyMarkup) {
            $sender->keyboard($this->replyMarkup);
        }

        if ($this->disableNotification) {
            $sender->silent();
        }

        if ($this->protectContent) {
            $sender->protect();
        }

        if ($this->replyToMessageId !== null) {
            $sender->replyTo($this->replyToMessageId);
        }

        return $sender->send();
    }

    /**
     * Reset state untuk reuse instance.
     */
    public function reset(): static
    {
        $this->resetCommonState();
        $this->text = null;
        $this->media = [];

        return $this;
    }
}
