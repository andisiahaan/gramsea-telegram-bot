<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Support\MediaHelper;
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;

/**
 * Fluent chaining sender untuk mengirim single media ke Telegram.
 * 
 * @example
 * MediaSender::make($bot)
 *     ->to($chatId)
 *     ->photo('https://example.com/image.jpg')
 *     ->caption('Check this out!')
 *     ->keyboard(InlineKeyboard::make()->callback('Like', 'like'))
 *     ->send();
 */
class MediaSender extends BaseSender
{
    protected ?string $mediaUrl = null;
    protected ?string $mediaType = null;
    protected ?string $caption = null;

    /**
     * Set media dengan auto-detect type.
     */
    public function media(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = MediaHelper::getMediaType($url);
        return $this;
    }

    /**
     * Set photo.
     */
    public function photo(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'photo';
        return $this;
    }

    /**
     * Set video.
     */
    public function video(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'video';
        return $this;
    }

    /**
     * Set audio.
     */
    public function audio(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'audio';
        return $this;
    }

    /**
     * Set document.
     */
    public function document(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'document';
        return $this;
    }

    /**
     * Set animation (GIF).
     */
    public function animation(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'animation';
        return $this;
    }

    /**
     * Set voice.
     */
    public function voice(string $url): static
    {
        $this->mediaUrl = $url;
        $this->mediaType = 'voice';
        return $this;
    }

    /**
     * Set caption. Mendukung markdown sederhana.
     */
    public function caption(string $caption): static
    {
        $this->caption = TextFormatter::markdownToHtml($caption);
        return $this;
    }

    /**
     * Set caption dengan HTML langsung tanpa konversi.
     */
    public function captionHtml(string $html): static
    {
        $this->caption = $html;
        return $this;
    }

    /**
     * Alias untuk caption(). Untuk konsistensi dengan MessageSender.
     */
    public function text(string $text): static
    {
        return $this->caption($text);
    }

    /**
     * Execute dan kirim media.
     * 
     * @return array Telegram API response
     * @throws \InvalidArgumentException Jika parameter tidak valid
     * @throws ApiException Jika Telegram API mengembalikan error
     */
    public function send(): array
    {
        $this->validateChatId();

        if (empty($this->mediaUrl) || empty($this->mediaType)) {
            throw new \InvalidArgumentException('Media is required. Use ->photo(), ->video(), etc.');
        }

        $methodName = 'send' . ucfirst($this->mediaType);

        $params = [
            'chat_id' => $this->chatId,
            $this->mediaType => $this->mediaUrl,
        ];

        if ($this->caption) {
            $params['caption'] = $this->caption;
            $params['parse_mode'] = $this->parseMode;
        }

        $this->applyCommonParams($params);

        return $this->bot->$methodName($params);
    }

    /**
     * Reset state untuk reuse instance.
     */
    public function reset(): static
    {
        $this->resetCommonState();
        $this->mediaUrl = null;
        $this->mediaType = null;
        $this->caption = null;

        return $this;
    }
}
