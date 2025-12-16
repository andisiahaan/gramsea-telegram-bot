<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;

/**
 * Fluent chaining sender untuk mengirim text message ke Telegram.
 * 
 * @example
 * TextSender::make($bot)
 *     ->to($chatId)
 *     ->text('Hello **world**!')
 *     ->keyboard(InlineKeyboard::make()->callback('Click', 'action'))
 *     ->send();
 */
class TextSender extends BaseSender
{
    protected ?string $text = null;
    
    // Link Preview Options
    protected bool $linkPreviewDisabled = false;
    protected ?string $linkPreviewUrl = null;
    protected bool $linkPreviewSmall = false;
    protected bool $linkPreviewLarge = false;
    protected bool $linkPreviewAboveText = false;

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
    // LINK PREVIEW OPTIONS
    // =========================================================================

    /**
     * Disable link preview.
     */
    public function noPreview(bool $disable = true): static
    {
        $this->linkPreviewDisabled = $disable;
        return $this;
    }

    /**
     * Alias untuk noPreview().
     */
    public function disableLinkPreview(bool $disable = true): static
    {
        return $this->noPreview($disable);
    }

    /**
     * Set URL untuk link preview.
     * Jika tidak di-set, URL pertama dalam text akan digunakan.
     */
    public function previewUrl(string $url): static
    {
        $this->linkPreviewUrl = $url;
        return $this;
    }

    /**
     * Prefer small media di link preview.
     */
    public function smallPreview(bool $small = true): static
    {
        $this->linkPreviewSmall = $small;
        if ($small) {
            $this->linkPreviewLarge = false;
        }
        return $this;
    }

    /**
     * Prefer large media di link preview.
     */
    public function largePreview(bool $large = true): static
    {
        $this->linkPreviewLarge = $large;
        if ($large) {
            $this->linkPreviewSmall = false;
        }
        return $this;
    }

    /**
     * Show link preview above text (default: below).
     */
    public function previewAboveText(bool $above = true): static
    {
        $this->linkPreviewAboveText = $above;
        return $this;
    }

    /**
     * Execute dan kirim text message.
     * 
     * @return array Telegram API response
     * @throws \InvalidArgumentException Jika parameter tidak valid
     * @throws ApiException Jika Telegram API mengembalikan error
     */
    public function send(): array
    {
        $this->validateChatId();

        if (empty($this->text)) {
            throw new \InvalidArgumentException('Text is required. Use ->text($text) method.');
        }

        $params = [
            'chat_id' => $this->chatId,
            'text' => $this->text,
            'parse_mode' => $this->parseMode,
        ];

        $this->applyCommonParams($params);
        $this->applyLinkPreviewOptions($params);

        return $this->bot->sendMessage($params);
    }

    /**
     * Apply link preview options ke params.
     */
    protected function applyLinkPreviewOptions(array &$params): void
    {
        $linkPreviewOptions = [];

        if ($this->linkPreviewDisabled) {
            $linkPreviewOptions['is_disabled'] = true;
        }

        if ($this->linkPreviewUrl !== null) {
            $linkPreviewOptions['url'] = $this->linkPreviewUrl;
        }

        if ($this->linkPreviewSmall) {
            $linkPreviewOptions['prefer_small_media'] = true;
        }

        if ($this->linkPreviewLarge) {
            $linkPreviewOptions['prefer_large_media'] = true;
        }

        if ($this->linkPreviewAboveText) {
            $linkPreviewOptions['show_above_text'] = true;
        }

        if (!empty($linkPreviewOptions)) {
            $params['link_preview_options'] = json_encode($linkPreviewOptions);
        }
    }

    /**
     * Reset state untuk reuse instance.
     */
    public function reset(): static
    {
        $this->resetCommonState();
        $this->text = null;
        $this->linkPreviewDisabled = false;
        $this->linkPreviewUrl = null;
        $this->linkPreviewSmall = false;
        $this->linkPreviewLarge = false;
        $this->linkPreviewAboveText = false;

        return $this;
    }
}
