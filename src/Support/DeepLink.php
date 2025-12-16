<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Support;

/**
 * Helper class untuk generate dan parse Telegram deep links.
 */
class DeepLink
{
    /**
     * Base URL untuk deep links.
     */
    protected const BASE_URL = 'https://t.me/';

    /**
     * Generate start link dengan parameter.
     * 
     * @param string $botUsername Username bot (tanpa @)
     * @param string|null $startParameter Parameter yang akan diterima di /start command
     * @return string Deep link URL
     * 
     * Example:
     * DeepLink::start('mybot', 'ref123') => 'https://t.me/mybot?start=ref123'
     */
    public static function start(string $botUsername, ?string $startParameter = null): string
    {
        $url = self::BASE_URL . ltrim($botUsername, '@');
        
        if ($startParameter !== null && $startParameter !== '') {
            $url .= '?start=' . urlencode($startParameter);
        }
        
        return $url;
    }

    /**
     * Generate startgroup link untuk add bot ke group.
     * 
     * @param string $botUsername Username bot
     * @param string|null $startParameter Parameter yang akan diterima
     * @return string Deep link URL
     */
    public static function startGroup(string $botUsername, ?string $startParameter = null): string
    {
        $url = self::BASE_URL . ltrim($botUsername, '@');
        
        if ($startParameter !== null && $startParameter !== '') {
            $url .= '?startgroup=' . urlencode($startParameter);
        } else {
            $url .= '?startgroup';
        }
        
        return $url;
    }

    /**
     * Generate startchannel link untuk add bot ke channel.
     * 
     * @param string $botUsername Username bot
     * @param string|null $startParameter Parameter yang akan diterima
     * @return string Deep link URL
     */
    public static function startChannel(string $botUsername, ?string $startParameter = null): string
    {
        $url = self::BASE_URL . ltrim($botUsername, '@');
        
        if ($startParameter !== null && $startParameter !== '') {
            $url .= '?startchannel=' . urlencode($startParameter);
        } else {
            $url .= '?startchannel';
        }
        
        return $url;
    }

    /**
     * Generate link ke chat/channel/group.
     * 
     * @param string $username Username chat (tanpa @)
     * @return string Deep link URL
     */
    public static function chat(string $username): string
    {
        return self::BASE_URL . ltrim($username, '@');
    }

    /**
     * Generate link ke message tertentu di public chat.
     * 
     * @param string $username Username chat
     * @param int $messageId ID pesan
     * @return string Deep link URL
     */
    public static function message(string $username, int $messageId): string
    {
        return self::BASE_URL . ltrim($username, '@') . '/' . $messageId;
    }

    /**
     * Generate link ke message di private chat/group.
     * 
     * @param int $chatId Chat ID (tanpa -100 prefix untuk supergroup)
     * @param int $messageId ID pesan
     * @return string Deep link URL
     */
    public static function privateMessage(int $chatId, int $messageId): string
    {
        // Remove -100 prefix jika ada
        $chatIdStr = (string) abs($chatId);
        if (str_starts_with($chatIdStr, '100')) {
            $chatIdStr = substr($chatIdStr, 3);
        }
        
        return self::BASE_URL . 'c/' . $chatIdStr . '/' . $messageId;
    }

    /**
     * Generate share link untuk share text/URL.
     * 
     * @param string $url URL yang akan di-share
     * @param string|null $text Text tambahan
     * @return string Deep link URL
     */
    public static function share(string $url, ?string $text = null): string
    {
        $shareUrl = self::BASE_URL . 'share/url?url=' . urlencode($url);
        
        if ($text !== null && $text !== '') {
            $shareUrl .= '&text=' . urlencode($text);
        }
        
        return $shareUrl;
    }

    /**
     * Parse start parameter dari deep link atau /start command text.
     * 
     * @param string $input Deep link URL atau command text (e.g., "/start ref123")
     * @return string|null Start parameter atau null jika tidak ada
     */
    public static function parseStartParameter(string $input): ?string
    {
        // Handle command text: "/start ref123" or "/start=ref123"
        if (str_starts_with($input, '/start')) {
            $input = trim(substr($input, 6));
            if ($input === '') {
                return null;
            }
            // Handle "/start=param" format
            if (str_starts_with($input, '=')) {
                return substr($input, 1);
            }
            // Handle "/start param" format
            return ltrim($input);
        }
        
        // Handle deep link URL
        $parsed = parse_url($input, PHP_URL_QUERY);
        if ($parsed === null || $parsed === false) {
            return null;
        }
        
        parse_str($parsed, $query);
        
        return $query['start'] ?? $query['startgroup'] ?? $query['startchannel'] ?? null;
    }

    /**
     * Generate referral code.
     * 
     * @param int|string $userId User ID atau identifier
     * @param string|null $prefix Optional prefix
     * @return string Referral code
     */
    public static function generateReferralCode(int|string $userId, ?string $prefix = null): string
    {
        $code = base64_encode((string) $userId);
        $code = rtrim(strtr($code, '+/', '-_'), '='); // URL-safe base64
        
        if ($prefix !== null) {
            $code = $prefix . '_' . $code;
        }
        
        return $code;
    }

    /**
     * Decode referral code.
     * 
     * @param string $code Referral code
     * @param string|null $prefix Expected prefix (akan di-strip)
     * @return string|null Decoded value atau null jika invalid
     */
    public static function decodeReferralCode(string $code, ?string $prefix = null): ?string
    {
        // Strip prefix jika ada
        if ($prefix !== null && str_starts_with($code, $prefix . '_')) {
            $code = substr($code, strlen($prefix) + 1);
        }
        
        // Decode URL-safe base64
        $code = strtr($code, '-_', '+/');
        $padLength = 4 - (strlen($code) % 4);
        if ($padLength < 4) {
            $code .= str_repeat('=', $padLength);
        }
        
        $decoded = base64_decode($code, true);
        
        return $decoded !== false ? $decoded : null;
    }
}
