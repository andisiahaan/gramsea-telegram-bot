<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;
use AndiSiahaan\GramseaTelegramBot\Support\MediaHelper;
use AndiSiahaan\GramseaTelegramBot\Support\TextFormatter;

/**
 * Fluent chaining sender untuk mengirim media group ke Telegram.
 * 
 * @example
 * MediaGroupSender::make($bot)
 *     ->to($chatId)
 *     ->photo('https://example.com/image1.jpg')
 *     ->photo('https://example.com/image2.jpg')
 *     ->caption('Album photos!')
 *     ->send();
 */
class MediaGroupSender extends BaseSender
{
    protected array $media = [];
    protected ?string $caption = null;

    /**
     * Add media dengan auto-detect type.
     */
    public function add(string $url): static
    {
        $this->media[] = [
            'type' => MediaHelper::getMediaType($url),
            'media' => $url,
        ];
        return $this;
    }

    /**
     * Add photo.
     */
    public function photo(string $url): static
    {
        $this->media[] = [
            'type' => 'photo',
            'media' => $url,
        ];
        return $this;
    }

    /**
     * Add video.
     */
    public function video(string $url): static
    {
        $this->media[] = [
            'type' => 'video',
            'media' => $url,
        ];
        return $this;
    }

    /**
     * Add document.
     */
    public function document(string $url): static
    {
        $this->media[] = [
            'type' => 'document',
            'media' => $url,
        ];
        return $this;
    }

    /**
     * Add audio.
     */
    public function audio(string $url): static
    {
        $this->media[] = [
            'type' => 'audio',
            'media' => $url,
        ];
        return $this;
    }

    /**
     * Add multiple media dari array.
     */
    public function media(array $urls): static
    {
        foreach ($urls as $url) {
            $this->add($url);
        }
        return $this;
    }

    /**
     * Set caption. Mendukung markdown sederhana.
     * Caption akan ditambahkan ke media pertama.
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
     * Execute dan kirim media group.
     * 
     * @return array Telegram API response
     * @throws \InvalidArgumentException Jika parameter tidak valid
     * @throws ApiException Jika Telegram API mengembalikan error
     */
    public function send(): array
    {
        $this->validateChatId();
        $this->validateMediaTypes();

        if (count($this->media) < 2) {
            throw new \InvalidArgumentException('Media group requires at least 2 media items.');
        }

        // Jika ada keyboard, kirim caption sebagai pesan terpisah
        if ($this->caption && $this->replyMarkup) {
            $captionParams = [
                'chat_id' => $this->chatId,
                'text' => $this->caption,
                'parse_mode' => $this->parseMode,
                'reply_markup' => json_encode($this->replyMarkup),
            ];

            if ($this->disableNotification) {
                $captionParams['disable_notification'] = true;
            }

            if ($this->protectContent) {
                $captionParams['protect_content'] = true;
            }

            $this->bot->sendMessage($captionParams);
            
            // Reset caption karena sudah dikirim
            $captionForMedia = null;
        } else {
            $captionForMedia = $this->caption;
        }

        // Prepare media group
        $mediaGroup = [];
        $isFirst = true;

        foreach ($this->media as $item) {
            $mediaItem = [
                'type' => $item['type'],
                'media' => $item['media'],
            ];

            // Add caption ke item pertama saja
            if ($isFirst && $captionForMedia) {
                $mediaItem['caption'] = $captionForMedia;
                $mediaItem['parse_mode'] = $this->parseMode;
                $isFirst = false;
            }

            $mediaGroup[] = $mediaItem;
        }

        $params = [
            'chat_id' => $this->chatId,
            'media' => json_encode($mediaGroup),
        ];

        if ($this->disableNotification) {
            $params['disable_notification'] = true;
        }

        if ($this->protectContent) {
            $params['protect_content'] = true;
        }

        if ($this->replyToMessageId !== null) {
            $params['reply_to_message_id'] = $this->replyToMessageId;
        }

        return $this->bot->sendMediaGroup($params);
    }

    /**
     * Reset state untuk reuse instance.
     */
    public function reset(): static
    {
        $this->resetCommonState();
        $this->media = [];
        $this->caption = null;

        return $this;
    }

    /**
     * Get current media count.
     */
    public function count(): int
    {
        return count($this->media);
    }

    /**
     * Validate that media types are compatible for grouping.
     * 
     * Rules:
     * - Photo + Video = OK (visual album)
     * - Document + Document = OK
     * - Audio + Audio = OK
     * - Document/Audio + Photo/Video = NOT OK
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateMediaTypes(): void
    {
        if (count($this->media) < 2) {
            return;
        }

        $types = array_column($this->media, 'type');
        $uniqueTypes = array_unique($types);

        // Define type groups
        $visualTypes = ['photo', 'video'];
        $documentTypes = ['document'];
        $audioTypes = ['audio'];

        $hasVisual = !empty(array_intersect($uniqueTypes, $visualTypes));
        $hasDocument = !empty(array_intersect($uniqueTypes, $documentTypes));
        $hasAudio = !empty(array_intersect($uniqueTypes, $audioTypes));

        // Count how many different groups are present
        $groupCount = ($hasVisual ? 1 : 0) + ($hasDocument ? 1 : 0) + ($hasAudio ? 1 : 0);

        if ($groupCount > 1) {
            $typeList = implode(', ', $uniqueTypes);
            throw new \InvalidArgumentException(
                "Cannot mix different media types in a group. " .
                "Photo/Video can be grouped together. " .
                "Documents can only be grouped with documents. " .
                "Audio can only be grouped with audio. " .
                "Found: {$typeList}"
            );
        }
    }
}

