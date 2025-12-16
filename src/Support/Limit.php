<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Constants untuk Telegram API limits.
 * 
 * Berguna untuk validasi sebelum mengirim ke API.
 */
class Limit
{
    // =============================================
    // Message Limits
    // =============================================

    /** Maximum text message length */
    public const MESSAGE_TEXT = 4096;

    /** Maximum caption length */
    public const CAPTION = 1024;

    /** Maximum callback_data length */
    public const CALLBACK_DATA = 64;

    /** Maximum inline keyboard buttons per row */
    public const INLINE_BUTTONS_PER_ROW = 8;

    /** Maximum inline keyboard rows */
    public const INLINE_KEYBOARD_ROWS = 100;

    /** Maximum reply keyboard buttons per row */
    public const REPLY_BUTTONS_PER_ROW = 12;

    /** Maximum reply keyboard rows */
    public const REPLY_KEYBOARD_ROWS = 100;

    // =============================================
    // File Limits
    // =============================================

    /** Maximum file download size (20 MB) */
    public const FILE_DOWNLOAD = 20 * 1024 * 1024;

    /** Maximum file upload size via URL (5 MB for photos) */
    public const PHOTO_UPLOAD = 5 * 1024 * 1024;

    /** Maximum file upload size (50 MB) */
    public const FILE_UPLOAD = 50 * 1024 * 1024;

    /** Maximum photo dimension (width + height) */
    public const PHOTO_DIMENSION_SUM = 10000;

    /** Maximum photo width/height ratio */
    public const PHOTO_RATIO = 20;

    // =============================================
    // Media Group Limits
    // =============================================

    /** Minimum media in group */
    public const MEDIA_GROUP_MIN = 2;

    /** Maximum media in group */
    public const MEDIA_GROUP_MAX = 10;

    // =============================================
    // Bot Limits
    // =============================================

    /** Maximum bot commands */
    public const BOT_COMMANDS = 100;

    /** Maximum command description length */
    public const COMMAND_DESCRIPTION = 256;

    /** Maximum bot description length */
    public const BOT_DESCRIPTION = 512;

    /** Maximum bot short description length */
    public const BOT_SHORT_DESCRIPTION = 120;

    /** Maximum bot name length */
    public const BOT_NAME = 64;

    // =============================================
    // Deep Link Limits
    // =============================================

    /** Maximum start parameter length */
    public const START_PARAMETER = 64;

    /** Start parameter allowed characters pattern */
    public const START_PARAMETER_PATTERN = '/^[A-Za-z0-9_-]+$/';

    // =============================================
    // Other Limits
    // =============================================

    /** Maximum poll question length */
    public const POLL_QUESTION = 300;

    /** Maximum poll option length */
    public const POLL_OPTION = 100;

    /** Maximum poll options count */
    public const POLL_OPTIONS = 10;

    /** Minimum poll options count */
    public const POLL_OPTIONS_MIN = 2;

    /** Maximum entities per message */
    public const MESSAGE_ENTITIES = 100;

    /** Maximum sticker set name length */
    public const STICKER_SET_NAME = 64;

    /** Maximum sticker emoji length */
    public const STICKER_EMOJI = 100;

    // =============================================
    // Rate Limits (requests per second)
    // =============================================

    /** Global rate limit (messages per second) */
    public const RATE_GLOBAL = 30;

    /** Rate limit per chat (messages per minute) */
    public const RATE_PER_CHAT = 20;

    /** Rate limit to same group (messages per minute) */
    public const RATE_PER_GROUP = 20;

    // =============================================
    // Helper Methods
    // =============================================

    /**
     * Truncate text to fit message limit.
     */
    public static function truncateMessage(string $text, string $suffix = '...'): string
    {
        return self::truncate($text, self::MESSAGE_TEXT, $suffix);
    }

    /**
     * Truncate text to fit caption limit.
     */
    public static function truncateCaption(string $text, string $suffix = '...'): string
    {
        return self::truncate($text, self::CAPTION, $suffix);
    }

    /**
     * Truncate text to specified length.
     */
    public static function truncate(string $text, int $maxLength, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $suffixLength = mb_strlen($suffix);
        return mb_substr($text, 0, $maxLength - $suffixLength) . $suffix;
    }

    /**
     * Check if text exceeds message limit.
     */
    public static function exceedsMessageLimit(string $text): bool
    {
        return mb_strlen($text) > self::MESSAGE_TEXT;
    }

    /**
     * Check if text exceeds caption limit.
     */
    public static function exceedsCaptionLimit(string $text): bool
    {
        return mb_strlen($text) > self::CAPTION;
    }

    /**
     * Split text into chunks that fit message limit.
     * 
     * @param string $text Text to split
     * @param int $maxLength Maximum length per chunk
     * @return array<string> Array of text chunks
     */
    public static function splitText(string $text, int $maxLength = self::MESSAGE_TEXT): array
    {
        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $lines = explode("\n", $text);
        $currentChunk = '';

        foreach ($lines as $line) {
            $potentialChunk = $currentChunk === '' ? $line : $currentChunk . "\n" . $line;
            
            if (mb_strlen($potentialChunk) > $maxLength) {
                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                }
                
                // Jika satu baris saja sudah melebihi limit
                if (mb_strlen($line) > $maxLength) {
                    // Split by words
                    $words = explode(' ', $line);
                    $currentChunk = '';
                    
                    foreach ($words as $word) {
                        $potentialChunk = $currentChunk === '' ? $word : $currentChunk . ' ' . $word;
                        
                        if (mb_strlen($potentialChunk) > $maxLength) {
                            if ($currentChunk !== '') {
                                $chunks[] = $currentChunk;
                            }
                            $currentChunk = $word;
                        } else {
                            $currentChunk = $potentialChunk;
                        }
                    }
                } else {
                    $currentChunk = $line;
                }
            } else {
                $currentChunk = $potentialChunk;
            }
        }

        if ($currentChunk !== '') {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    /**
     * Validate start parameter.
     */
    public static function isValidStartParameter(string $parameter): bool
    {
        if (mb_strlen($parameter) > self::START_PARAMETER) {
            return false;
        }

        return preg_match(self::START_PARAMETER_PATTERN, $parameter) === 1;
    }
}
